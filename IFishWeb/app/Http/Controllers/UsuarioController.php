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
use Illuminate\Support\Str;
use App\Mail\UserInvitationMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\ResetPasswordMail;

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
     * Muestra el formulario para invitar a un nuevo Dueño.
     */
    public function create()
    {
        // La vista ahora es para invitar, no necesita datos extra.
        return view('usuarios.create');
    }

    /**
     * Guarda el nuevo Dueño sin contraseña, le genera un token de invitación,
     * y crea su primer criadero.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'rol' => ['required', Rule::in(['dueno', 'admin'])],

            // ✅ Validación condicional con closure
            'nombre_criadero' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->rol === 'dueno') {
                        if (empty($value)) {
                            $fail('El nombre del criadero es obligatorio para los dueños.');
                        } elseif (!is_string($value)) {
                            $fail('El nombre del criadero debe ser una cadena de texto.');
                        } elseif (strlen($value) > 255) {
                            $fail('El nombre del criadero no debe exceder 255 caracteres.');
                        } elseif (Criadero::where('nombre', $value)->exists()) {
                            $fail('El nombre del criadero ya está registrado.');
                        }
                    }
                },
            ],

            'ubicacion' => ['nullable', 'string', 'max:255'],
        ]);

        $nuevoUsuario = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => null,
                'rol' => $request->rol === 'dueno' ? 'Dueño' : 'Admin',
                'invitation_token' => Str::random(60),
                'invitation_expires_at' => now()->addDays(2),
            ]);

            if ($request->rol === 'dueno') {
                Criadero::create([
                    'nombre' => $request->nombre_criadero,
                    'ubicacion' => $request->ubicacion,
                    'user_id' => $user->id,
                ]);
            }

            return $user;
        });

        Mail::to($nuevoUsuario->email)->send(new UserInvitationMail($nuevoUsuario));

        return redirect()
            ->route('superadmin.usuarios.index')
            ->with('success', 'Invitación enviada exitosamente. El usuario recibirá un correo para establecer su contraseña.');
    }

    /**
     * Muestra el formulario para editar un usuario.
     */
    public function edit(User $usuario)
    {

        $criaderosDelUsuario = $usuario->criaderos()->orderBy('nombre')->get();

         return view('usuarios.edit', compact('usuario', 'criaderosDelUsuario'));
    }

    /**
     * Actualiza un usuario en la base de datos (sin tocar la contraseña).
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            // La validación para 'rol' se elimina porque ya no es editable.
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Preparamos los datos para actualizar (solo los permitidos).
        $data = $request->only('name', 'email');
        
        // Solo actualizamos la contraseña si el usuario escribió una nueva.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
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

    /**
     * Envía un enlace de reseteo de contraseña al usuario.
     */
    public function sendPasswordReset(Request $request, User $usuario)
    {
        // Generamos el token de reset
        $token = Password::broker()->createToken($usuario);

        // Construimos la URL del reset
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $usuario->email,
        ], false));

        // Enviamos el correo con tu Mailable personalizado
        Mail::to($usuario->email)->send(new ResetPasswordMail($usuario, $resetUrl));

        return back()->with('success', 'Se ha enviado un enlace de recuperación de contraseña al usuario.');
    }

}
