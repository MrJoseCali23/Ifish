<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCriaderoIsSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->rol === 'Dueño') {
            
            // Si no hay criadero activo y no está en la ruta de selección
            if (!session()->has('active_criadero_id') && !$request->routeIs('criaderos.select')) {
                
                // 🔹 Si la petición es AJAX o API (fetch), devolvemos un JSON en lugar de redirigir
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Debe seleccionar un criadero antes de continuar.'
                    ], 403);
                }

                // 🔹 Para las rutas normales (web), sigue redirigiendo como siempre
                return redirect()->route('criaderos.select');
            }
        }

        return $next($request);
    }
}
