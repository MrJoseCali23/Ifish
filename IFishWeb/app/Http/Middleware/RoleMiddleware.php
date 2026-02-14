<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ ESTA LÍNEA FALTABA
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Verifica que el usuario tenga uno de los roles permitidos.
     *
     * Uso en rutas:
     * Route::get('/ruta', [Controller::class, 'metodo'])->middleware('role:Admin,Dueño');
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user || !in_array($user->rol, $roles)) {
            abort(403, 'Acción no autorizada.');
        }

        return $next($request);
    }
}
