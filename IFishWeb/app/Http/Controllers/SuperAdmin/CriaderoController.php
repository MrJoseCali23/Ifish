<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criadero;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Dispensador;
use App\Models\DispensadorEvento;
use Illuminate\Support\Facades\Auth;

class CriaderoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'activos'); // Por defecto, mostramos los activos

        $query = Criadero::with('owner')->orderBy('nombre', 'asc');

        if ($status == 'archivados') {
            $query->where('estado', 'Archivado');
        } else {
            $query->where('estado', '!=', 'Archivado');
        }

        $criaderos = $query->paginate(15)->withQueryString();

        return view('superadmin.criaderos.index', compact('criaderos', 'status'));
    }
    public function archive(Criadero $criadero)
    {
        DB::transaction(function () use ($criadero) {
            // 1. Archivamos el criadero
            $criadero->update([
                'estado' => 'Archivado',
                'archivado_at' => now(), // Suponiendo que añadimos esta columna
            ]);

            // 2. Ponemos todos sus dispensadores como 'Inactivo'
            $criadero->dispensadores()->update(['estado' => 'Inactivo']);
        });

        return redirect()->route('superadmin.criaderos.index')->with('success', "El criadero '{$criadero->nombre}' y sus dispensadores han sido archivados.");
    }

    public function restore(Criadero $criadero)
    {
        DB::transaction(function () use ($criadero) {
            // 1. Restauramos el criadero
            $criadero->update([
                'estado' => 'Activo',
                'archivado_at' => null,
            ]);

            // 2. Volvemos a poner sus dispensadores como 'Activo'
            $criadero->dispensadores()->update(['estado' => 'Activo']);
        });
        
        return redirect()->route('superadmin.criaderos.index', ['status' => 'archivados'])->with('success', "El criadero '{$criadero->nombre}' y sus dispensadores han sido restaurados.");
    }
    public function create()
    {
        // Buscamos usuarios que puedan ser dueños (Rol 'Dueño' y que no tengan ya un criadero)
        $dueñosDisponibles = User::where('rol', 'Dueño')->whereNull('criadero_id')->get();
        return view('superadmin.criaderos.create', compact('dueñosDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_criadero' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            // 1. Creamos el nuevo usuario con el rol de "Dueño"
            $dueño = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'Dueño',
            ]);

            // 2. Creamos el criadero y lo asignamos al dueño que acabamos de crear
            $criadero = Criadero::create([
                'nombre' => $request->nombre_criadero,
                'ubicacion' => $request->ubicacion,
                'user_id' => $dueño->id,
            ]);

            // 3. Finalmente, actualizamos al usuario para darle su criadero_id
            $dueño->criadero_id = $criadero->id;
            $dueño->save();
        });

        return redirect()->route('superadmin.criaderos.index')->with('success', 'Criadero y Dueño creados exitosamente.');
    }

    public function edit(Criadero $criadero)
    {
        // Buscamos todos los dueños, incluyendo el actual, por si se quiere cambiar
        $dueños = User::where('rol', 'Dueño')->get();
        return view('superadmin.criaderos.edit', compact('criadero', 'dueños'));
    }

    public function update(Request $request, Criadero $criadero)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'ubicacion' => 'nullable|string|max:255',
            'estado' => 'required|in:Activo,Inactivo,Suspendido',
        ]);

        DB::transaction(function () use ($request, $criadero) {
            // Desasignamos el criadero del dueño antiguo si ha cambiado
            if ($criadero->user_id != $request->user_id) {
                User::find($criadero->user_id)->update(['criadero_id' => null]);
            }

            // Actualizamos el criadero
            $criadero->update($request->all());

            // Asignamos el criadero al nuevo dueño
            User::find($request->user_id)->update(['criadero_id' => $criadero->id]);
        });

        return redirect()->route('superadmin.criaderos.index')->with('success', 'Criadero actualizado exitosamente.');
    }

    public function showAssignForm(Criadero $criadero)
    {
        // Buscamos solo los dispensadores que NO tienen un criadero_id asignado.
        $dispensadoresSinAsignar = Dispensador::whereNull('criadero_id')->get();

        return view('superadmin.criaderos.assign', compact('criadero', 'dispensadoresSinAsignar'));
    }

    /**
     * Procesa la asignación de un dispensador a un criadero.
     */
    public function assignDispenser(Request $request, Criadero $criadero)
    {
        $request->validate([
            'dispensador_id' => 'required|exists:Dispensadores,id_dispensador'
        ]);

        $dispensador = Dispensador::find($request->dispensador_id);
        $dispensador->update(['criadero_id' => $criadero->id]);

        // ▼▼▼ LÓGICA AÑADIDA PARA EL HISTORIAL ▼▼▼
        DispensadorEvento::create([
            'dispensador_id' => $dispensador->id_dispensador,
            'tipo_evento' => 'asignacion_criadero',
            'descripcion' => "Dispensador asignado al criadero '{$criadero->nombre}'.",
            'user_id' => Auth::id(), // El Super Admin que realizó la acción
        ]);

        return redirect()->route('superadmin.criaderos.index')->with('success', "Dispensador asignado a {$criadero->nombre} exitosamente.");
    }
}