<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CriaderoScope implements Scope
{
    /**
     * Aplica el scope a una consulta Eloquent dada.
     */
    public function apply(Builder $builder, Model $model)
    {
        // Verificamos si hay un usuario autenticado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Si el usuario NO es un Super Admin Y tiene un criadero_id asignado...
            if ($user->rol !== 'Admin' && $user->criadero_id) {
                // ...aplicamos el filtro a la consulta actual.
                // Ej: SELECT * FROM estanques WHERE criadero_id = 5
                $builder->where($model->getTable() . '.criadero_id', $user->criadero_id);
            }
            
            // Si el usuario es un 'Admin' (nuestro Super Admin), no se aplica ningún filtro,
            // por lo que podrá ver los datos de todos los criaderos.
        }
    }
}
