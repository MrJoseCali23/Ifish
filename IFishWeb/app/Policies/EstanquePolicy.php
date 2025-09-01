<?php

namespace App\Policies;

use App\Models\Estanque;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EstanquePolicy
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
     * Determina si un usuario puede crear un estanque.
     */
    public function create(User $user): bool
    {
        // Solo los dueños de criadero pueden crear estanques.
        return $user->rol === 'Dueño';
    }

    /**
     * Determina si un usuario puede actualizar un estanque.
     */
    public function update(User $user, Estanque $estanque): bool
    {
        // Un usuario puede actualizar un estanque si es el dueño
        // Y si el estanque pertenece a su propio criadero.
        return $user->rol === 'Dueño' && $user->criadero_id === $estanque->criadero_id;
    }

    /**
     * Determina si un usuario puede eliminar un estanque.
     */
    public function delete(User $user, Estanque $estanque): bool
    {
        // Aplicamos la misma regla que para actualizar.
        return $user->rol === 'Dueño' && $user->criadero_id === $estanque->criadero_id;
    }
}
