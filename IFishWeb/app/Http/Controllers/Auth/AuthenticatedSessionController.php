<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // --- INICIO DE LA NUEVA LÓGICA INTELIGENTE ---

        // Si el usuario es un "Dueño"
        if ($user->rol === 'Dueño') {
            $criaderos = $user->criaderos;

            // Si solo tiene UN criadero, lo seleccionamos automáticamente
            if ($criaderos->count() === 1) {
                // Guardamos el ID del único criadero en la sesión
                session(['active_criadero_id' => $criaderos->first()->id]);
                // Y lo enviamos directo al dashboard
                return redirect(RouteServiceProvider::HOME);
            }
            
            // Si tiene más de uno, o ninguno, lo enviamos a la página de selección
            return redirect()->route('criaderos.select');
        }

        // Si es Super Admin o cualquier otro rol, va al dashboard normal
        return redirect(RouteServiceProvider::HOME);
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