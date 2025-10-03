<?php

namespace App\Http\Controllers;

use App\Models\Criadero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CriaderoSelectorController extends Controller
{
    /**
     * Muestra la página para que el usuario elija en qué criadero trabajar.
     */
    public function showSelection()
    {
        // Obtenemos todos los criaderos que pertenecen al usuario actual
        $criaderos = Auth::user()->criaderos;

        return view('public.criaderos.select', compact('criaderos'));
    }

    /**
     * Guarda el criadero seleccionado en la sesión del usuario y lo redirige al dashboard.
     */
    public function selectCriadero(Criadero $criadero)
    {
        // Seguridad #1: Nos aseguramos de que el criadero seleccionado realmente pertenezca al usuario.
        if ($criadero->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // ▼▼▼ NUEVA REGLA DE SEGURIDAD ▼▼▼
        // Seguridad #2: Nos aseguramos de que el criadero esté 'Activo'.
        if ($criadero->estado !== 'Activo') {
            return redirect()->route('criaderos.select')
                   ->with('error', "No se puede acceder al criadero '{$criadero->nombre}' porque no está activo.");
        }

        // ¡La parte mágica! Guardamos el ID del criadero en la sesión del usuario.
        session(['active_criadero_id' => $criadero->id]);

        return redirect()->route('dashboard');
    }
}
