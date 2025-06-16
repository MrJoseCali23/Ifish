<?php

// app/Http/Middleware/IsAdmin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Comprobamos si el usuario ha iniciado sesión Y si su rol es 'Admin'
        if (auth()->check() && auth()->user()->rol === 'Admin') {
            // Si cumple las condiciones, le dejamos pasar a la ruta solicitada.
            return $next($request);
        }

        // Si no es un Admin, lo redirigimos al dashboard con un mensaje de error.
        return redirect('/dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}
