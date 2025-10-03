<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Criadero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    /**
     * Muestra la lista de usuarios para el Super Admin.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'activos');
        $query = User::orderBy('name', 'asc');

        if ($status == 'inactivos') {
            $query->where('estado', 'Inactivo');
        } else {
            $query->where('estado', 'Activo');
        }

        $usuarios = $query->paginate(15)->withQueryString();

        return view('usuarios.index', compact('usuarios', 'status'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nombre_criadero' => ['required', 'string', 'max:255', 'unique:criaderos,nombre'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request) {
            $dueño = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'Dueño',
            ]);

            $criadero = Criadero::create([
                'nombre' => $request->nombre_criadero,
                'ubicacion' => $request->ubicacion,
                'user_id' => $dueño->id,
            ]);

            $dueño->criadero_id = $criadero->id;
            $dueño->save();
        });

        return redirect()->route('superadmin.usuarios.index')->with('success', 'Dueño y su primer criadero han sido creados exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario.
     */
    public function edit(User $usuario)
    {
        // Cargar los criaderos asignados al usuario (solo para Dueños)
        $criaderos = $usuario->rol === 'Dueño' ? $usuario->criaderos()->orderBy('nombre')->get() : collect();
        return view('usuarios.edit', compact('usuario', 'criaderos'));
    }

    /**
     * Actualiza un usuario en la base de datos.
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'rol' => ['required', 'in:Admin,Dueño'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->only('name', 'email', 'rol');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->rol === 'Admin') {
            $data['criadero_id'] = null; // Limpia criadero_id si cambia a Admin
        }
        $usuario->update($data);

        return redirect()->route('superadmin.usuarios.index')->with('success', '¡Usuario actualizado exitosamente!');
    }

    /**
     * Desactiva un usuario y, si es un Dueño, archiva todos sus criaderos.
     */
    public function destroy(User $usuario)
    {
        DB::transaction(function () use ($usuario) {
            $usuario->update(['estado' => 'Inactivo']);
            if ($usuario->rol === 'Dueño') {
                $usuario->criaderos()->update(['estado' => 'Archivado']);
            }
        });

        $mensaje = "La cuenta del usuario {$usuario->name} ha sido desactivada.";
        if ($usuario->rol === 'Dueño') {
            $mensaje .= " Todos sus criaderos han sido archivados.";
        }

        return redirect()->route('superadmin.usuarios.index')->with('success', $mensaje);
    }

    /**
     * Reactiva a un usuario y a todos sus criaderos.
     */
    public function restore($userId)
    {
        $usuario = User::findOrFail($userId);

        DB::transaction(function () use ($usuario) {
            $usuario->update(['estado' => 'Activo']);
            if ($usuario->rol === 'Dueño') {
                $usuario->criaderos()->update(['estado' => 'Activo']);
            }
        });

        $mensaje = "La cuenta del usuario {$usuario->name} ha sido reactivada.";
        if ($usuario->rol === 'Dueño') {
            $mensaje .= " Todos sus criaderos han sido reactivados.";
        }

        return redirect()->route('superadmin.usuarios.index', ['status' => 'inactivos'])->with('success', $mensaje);
    }
}