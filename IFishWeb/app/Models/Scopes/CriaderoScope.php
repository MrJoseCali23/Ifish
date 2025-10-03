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
            
            // Si el usuario NO es un Super Admin...
            if ($user->rol !== 'Admin') {
                // ...obtenemos la lista de IDs de todos los criaderos que le pertenecen.
                $criaderoIds = $user->criaderos()->pluck('id')->toArray();

                // Aplicamos el filtro a la consulta actual.
                // Ej: SELECT * FROM estanques WHERE criadero_id IN (1, 5, 12)
                $builder->whereIn($model->getTable() . '.criadero_id', $criaderoIds);
            }
            
            // Si el usuario es un 'Admin' (Super Admin), no se aplica ningún filtro.
        }
    }
}