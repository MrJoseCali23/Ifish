<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // <-- Usa el LoginRequest correcto
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Laravel intenta autenticar al usuario (comprueba email y contraseña)
        $request->authenticate();

        // 2. ▼▼▼ NUESTRO BLOQUE DE SEGURIDAD ▼▼▼
        // Si la autenticación fue exitosa, AHORA revisamos su estado.
        if (Auth::user()->estado === 'Inactivo') {
            
            // Si está inactivo, cerramos la sesión que se acaba de crear
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Auth::logout();

            // Y lo devolvemos al login con un mensaje de error claro
            return back()->withErrors([
                'email' => 'Esta cuenta ha sido desactivada. Por favor, contacta al administrador.',
            ]);
        }
        // ▲▲▲ FIN DEL BLOQUE DE SEGURIDAD ▲▲▲

        // 3. Si todo está bien y el usuario está activo, regeneramos la sesión y lo dejamos entrar
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
