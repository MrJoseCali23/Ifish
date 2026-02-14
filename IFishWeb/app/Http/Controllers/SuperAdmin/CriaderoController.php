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
        $status = $request->query('status', 'activos');

        // Empezamos la consulta, asegurando que siempre cargue la relación con el dueño.
        $query = Criadero::with('owner')->whereHas('owner');

        if ($status == 'archivados') {
            // Un criadero se considera "archivado" si su propio estado es 'Archivado'
            // O si el estado de su dueño es 'Inactivo'.
            $query->where(function ($q) {
                $q->where('estado', 'Archivado')
                  ->orWhereHas('owner', function ($subQ) {
                      $subQ->where('estado', 'Inactivo');
                  });
            });
        } else { // 'activos' y 'suspendidos'
            // Un criadero se considera "activo" solo si su estado NO es 'Archivado'
            // Y, muy importante, si su dueño también está 'Activo'.
            $query->where('estado', '!=', 'Archivado')
                  ->whereHas('owner', function ($q) {
                      $q->where('estado', 'Activo');
                  });
        }
        
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        if (in_array($sort, ['nombre', 'created_at'])) {
            $query->orderBy($sort, $direction);
        }

        // Agrupamos los resultados por el nombre del dueño para la vista de acordeón.
        $criaderosPorDueño = $query->get()->groupBy('owner.name');

        return view('superadmin.criaderos.index', compact('criaderosPorDueño', 'status'));
    }

    public function create()
    {

        $dueños = User::where('rol', 'Dueño')
                      ->where('estado', 'Activo')
                      ->orderBy('name')
                      ->get();
        
        return view('superadmin.criaderos.create', compact('dueños'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:criaderos,nombre',
            'user_id' => 'required|exists:users,id', // Validamos que el dueño seleccionado exista
            'ubicacion' => 'nullable|string|max:255',
        ]);

        Criadero::create([
            'nombre' => $request->nombre,
            'ubicacion' => $request->ubicacion,
            'user_id' => $request->user_id, // Asignamos el dueño seleccionado en el formulario
            'estado' => 'Activo', // Por defecto, un nuevo criadero está activo
        ]);

        return redirect()->route('superadmin.criaderos.index')->with('success', 'Nuevo criadero asignado exitosamente.');
    }

    public function edit(Criadero $criadero)
    {
        $dueños = User::where('rol', 'Dueño')
                      ->where('estado', 'Activo')
                      ->orWhere('id', $criadero->user_id)
                      ->orderBy('name')
                      ->get();

        return view('superadmin.criaderos.edit', compact('criadero', 'dueños'));
    }

    public function update(Request $request, Criadero $criadero)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'ubicacion' => 'nullable|string|max:255',
            
            // Añadimos 'Archivado' a la lista de valores permitidos.
            'estado' => 'required|in:Activo,Suspendido,Archivado',
        ]);

        DB::transaction(function () use ($request, $criadero) {
            // Desasignamos el criadero del due\u00f1o antiguo si ha cambiado
            if ($criadero->user_id != $request->user_id) {
            }

            // Actualizamos el criadero
            $criadero->update($request->all());

            // Asignamos el criadero al nuevo dueño
        });

        return redirect()->route('superadmin.criaderos.index')->with('success', 'Criadero actualizado exitosamente.');
    }

    public function destroy(Criadero $criadero)
    {
        // Gracias a onDelete('cascade'), al borrar el criadero, se borrarán
        // todos sus usuarios, estanques, dispensadores, etc.
        $criadero->delete();
        return redirect()->route('superadmin.criaderos.index')->with('success', 'Criadero y todos sus datos asociados han sido eliminados.');
    }
}