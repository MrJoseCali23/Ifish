<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class InvitationController extends Controller
{
    /**
     * Muestra la página para que un usuario invitado establezca su contraseña.
     */
    public function accept($token)
{
    // Buscamos al usuario por su token de invitación
    $user = User::where('invitation_token', $token)->first();

    // Verificamos que el token exista, no haya expirado y no se haya usado
    if (!$user || $user->invitation_expires_at < now()) {
        return redirect()->route('login')->with('error', 'El enlace de invitación no es válido, ha expirado o ya fue utilizado.');
    }

    // Verificamos si el usuario ya tiene contraseña
    if ($user->password) {
        return redirect()->route('login')->with('error', 'Este enlace ya fue usado para establecer una contraseña. Inicia sesión con tu cuenta.');
    }

    // Si es válido, mostramos la vista con el formulario
    return view('auth.accept-invitation', [
        'token' => $token,
        'email' => $user->email, // <-- Aquí enviamos el email
    ]);
}


    /**
     * Guarda la nueva contraseña del usuario.
     */
    public function setPassword(Request $request, $token)
    {
        // Buscamos al usuario por su token de invitación
        $user = User::where('invitation_token', $token)->first();

        // Verificamos que el token exista, no haya expirado y no se haya usado
        if (!$user || $user->invitation_expires_at < now()) {
            return redirect()->route('login')->with('error', 'El enlace de invitación no es válido, ha expirado o ya fue utilizado.');
        }

        // Verificamos si el usuario ya tiene contraseña
        if ($user->password) {
            return redirect()->route('login')->with('error', 'Este enlace ya fue usado para establecer una contraseña. Inicia sesión con tu cuenta.');
        }

        // Validamos que la contraseña sea segura y que coincida la confirmación
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Guardamos la nueva contraseña encriptada
        $user->password = Hash::make($request->password);
        // Limpiamos el token para que no se pueda usar de nuevo
        $user->invitation_token = null;
        $user->invitation_expires_at = null;
        $user->save();

        // Iniciamos la sesión del nuevo usuario
        Auth::login($user);

        // Redirigimos al dashboard con un mensaje de éxito
        return redirect()->route('dashboard')->with('success', '¡Contraseña creada! Bienvenido a iFish.');
    }
}