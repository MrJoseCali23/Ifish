<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Criadero;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'activos'); // Por defecto, mostramos los activos

        $query = User::with('criadero')->orderBy('name', 'asc');

        if ($status == 'inactivos') {
            $query->where('estado', 'Inactivo');
        } else {
            $query->where('estado', 'Activo');
        }

        $usuarios = $query->paginate(15)->withQueryString();

        return view('usuarios.index', compact('usuarios', 'status'));
}
    public function create()
    {
        // Buscamos todos los criaderos para poder listarlos en un menú desplegable.
        $criaderos = Criadero::all();

        // Pasamos la lista de criaderos a la vista.
        // Asegúrate de que la ruta de la vista sea la correcta.
        // Si tu vista está en 'public/usuarios', usa 'public.usuarios.create'.
        return view('usuarios.create', compact('criaderos'));
    }

    public function store(Request $request)
    {
        // 1. --- VALIDACIÓN DE DATOS ---
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => ['required', 'in:Admin,Dueño'],

            // Regla inteligente: el criadero_id es requerido SI el rol es Dueño o Trabajador.
            'criadero_id' => ['nullable', 'required_if:rol,Dueño', 'exists:criaderos,id'],
        ], [
            // Mensajes de error personalizados en español
            'criadero_id.required_if' => 'Debe seleccionar un criadero para el rol de Dueño',
        ]);

        // 2. --- CREACIÓN DEL USUARIO ---
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            // Si el rol es 'Admin', criadero_id será null.
            // Si es 'Dueño' o 'Trabajador', tomará el valor del formulario.
            'criadero_id' => $request->criadero_id, 
        ]);

        // 3. --- REDIRECCIÓN ---
        return redirect()->route('superadmin.usuarios.index')->with('success', '¡Usuario creado exitosamente!');
    }

    /**
 * Muestra el formulario para editar un usuario.
 */
    public function edit(User $usuario)
    {
        // Buscamos todos los criaderos para el menú desplegable
        $criaderos = Criadero::all();

        return view('usuarios.edit', compact('usuario', 'criaderos'));
    }

    /**
     * Actualiza un usuario en la base de datos.
     */
    public function update(Request $request, User $usuario)
    {
        // 1. --- VALIDACIÓN ---
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // La regla 'unique' debe ignorar al email del usuario actual
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'rol' => ['required', 'in:Admin,Dueño,Trabajador'],
            'criadero_id' => ['nullable', 'required_if:rol,Dueño', 'required_if:rol,Trabajador', 'exists:criaderos,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // La contraseña es opcional
        ]);

        // 2. --- PREPARAR LOS DATOS ---
        $data = $request->only('name', 'email', 'rol', 'criadero_id');

        // Si el rol es 'Admin', nos aseguramos de que criadero_id sea nulo
        if ($request->rol === 'Admin') {
            $data['criadero_id'] = null;
        }

        // Solo actualizamos la contraseña si el usuario escribió una nueva
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 3. --- ACTUALIZAR EN LA BASE DE DATOS ---
        $usuario->update($data);

        // 4. --- REDIRECCIÓN ---
        return redirect()->route('superadmin.usuarios.index')->with('success', '¡Usuario actualizado exitosamente!');
    }

    public function destroy(User $usuario)
{
    // Usamos una transacción para asegurar que ambas operaciones se completen
    DB::transaction(function () use ($usuario) {
        
        // 1. Desactivamos al usuario, como antes
        $usuario->update(['estado' => 'Inactivo']);

        // 2. ▼▼▼ NUEVA LÓGICA INTELIGENTE ▼▼▼
        // Si el usuario que estamos desactivando es un 'Dueño' Y tiene un criadero asociado...
        if ($usuario->rol === 'Dueño' && $usuario->criadero) {
            // ...entonces también archivamos su criadero.
            $usuario->criadero->update(['estado' => 'Archivado']);
        }
    });

    // Construimos un mensaje de éxito dinámico para el Super Admin
    $mensaje = "La cuenta del usuario {$usuario->name} ha sido desactivada.";
    if ($usuario->rol === 'Dueño' && $usuario->criadero) {
        $mensaje .= " Su criadero también ha sido archivado.";
    }

    return redirect()->route('superadmin.usuarios.index')->with('success', $mensaje);
}
}
