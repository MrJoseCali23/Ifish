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
     * Determina si un usuario puede ver la LISTA de estanques.
     *
     * ▼▼▼ MÉTODO NUEVO ▼▼▼
     */
    public function viewAny(User $user): bool
    {
        // Permitimos que cualquier usuario logueado (Dueño) vea la página de la lista.
        // El GlobalScope se encargará de filtrar qué estanques específicos ve.
        return $user->rol === 'Dueño';
    }

    /**
     * Determina si un usuario puede ver UN estanque específico.
     *
     * ▼▼▼ MÉTODO NUEVO ▼▼▼
     */
    public function view(User $user, Estanque $estanque): bool
    {
        // Un usuario puede ver un estanque si pertenece a uno de sus criaderos.
        return $user->criaderos()->where('id', $estanque->criadero_id)->exists();
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
        // Y si el estanque pertenece a uno de sus criaderos.
        return $user->rol === 'Dueño' && $user->criaderos()->where('id', $estanque->criadero_id)->exists();
    }

    /**
     * Determina si un usuario puede eliminar un estanque.
     */
    public function delete(User $user, Estanque $estanque): bool
    {
        // Aplicamos la misma regla que para actualizar.
        return $user->rol === 'Dueño' && $user->criaderos()->where('id', $estanque->criadero_id)->exists();
    }
}