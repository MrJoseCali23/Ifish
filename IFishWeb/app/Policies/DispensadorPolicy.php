<?php

namespace App\Policies;

use App\Models\Dispensador;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DispensadorPolicy
{
    use HandlesAuthorization;

    /**
     * Permite que el Super Admin pueda hacer cualquier cosa.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->rol === 'Admin') {
            return true;
        }
        return null;
    }
    
    /**
     * Determina si un usuario puede ver la lista de dispensadores.
     * (Cualquier usuario logueado puede)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si un usuario puede actualizar un dispensador.
     */
    public function update(User $user, Dispensador $dispensador): bool
    {
        // Un Dueño puede actualizar un dispensador si este pertenece a
        // CUALQUIERA de los criaderos que posee.
        return $user->criaderos()->where('id', $dispensador->criadero_id)->exists();
    }
    
    /**
     * Determina si un usuario puede realizar una alimentación manual.
     */
    public function manualFeed(User $user, Dispensador $dispensador): bool
    {
        // Aplicamos la misma regla que para actualizar.
        return $user->criaderos()->where('id', $dispensador->criadero_id)->exists();
    }

    /**
     * Determina si un usuario puede eliminar un dispensador.
     */
    public function delete(User $user, Dispensador $dispensador): bool
    {
        // Solo el Super Admin puede eliminar dispensadores.
        return false;
    }
}