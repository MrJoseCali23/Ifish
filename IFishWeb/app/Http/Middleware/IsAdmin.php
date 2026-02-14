<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ Importamos correctamente Auth
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Verifica si el usuario autenticado es un administrador.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->rol === 'Admin') {
            return $next($request);
        }

        // Si no es admin, mostramos error 403
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
