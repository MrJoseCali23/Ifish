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
     * (Cualquier usuario logueado puede, el Global Scope se encarga de filtrar).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si un usuario puede ver un dispensador específico.
     */
    public function view(User $user, Dispensador $dispensador): bool
    {
        // Un usuario puede ver un dispensador si pertenece a su criadero.
        return $user->criadero_id === $dispensador->criadero_id;
    }

    /**
     * Determina si un usuario puede crear un dispensador.
     * ¡REGLA CLAVE!
     */
    public function create(User $user): bool
    {
        // Nadie, excepto el Super Admin (manejado por el 'before'), puede crear dispensadores.
        return false;
    }

    /**
     * Determina si un usuario puede actualizar un dispensador.
     */
    public function update(User $user, Dispensador $dispensador): bool
    {
        // Un Dueño o Trabajador puede editar un dispensador si pertenece a su criadero.
        return $user->criadero_id === $dispensador->criadero_id;
    }

    /**
     * Determina si un usuario puede eliminar un dispensador.
     */
    public function delete(User $user, Dispensador $dispensador): bool
    {
        // Solo el Super Admin puede eliminar dispensadores. El 'before' se encarga de darle
        // permiso. Para todos los demás (Dueños), devolvemos 'false'.
        return false;
    }
    
    /**
     * Determina si un usuario puede realizar una alimentación manual.
     */
    public function manualFeed(User $user, Dispensador $dispensador): bool
    {
        // Un Dueño o Trabajador puede alimentar manualmente un dispensador de su criadero.
        return $user->criadero_id === $dispensador->criadero_id;
    }
}
