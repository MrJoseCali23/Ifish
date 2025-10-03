<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema; // <-- Importante: añadimos Schema

class CriaderoScope implements Scope
{
    /**
     * Aplica el scope a una consulta Eloquent dada.
     */
    public function apply(Builder $builder, Model $model)
    {
        // Verificamos si hay un usuario autenticado y si no es un Super Admin
        if (Auth::check() && Auth::user()->rol !== 'Admin') {
            $user = Auth::user();
            $criaderoIds = $user->criaderos()->pluck('id')->toArray();

            // --- INICIO DE LA LÓGICA INTELIGENTE ---

            // CASO 1: El modelo tiene una columna 'criadero_id' directa
            // (Funciona para Estanques, Dispensadores, Tipos de Comida)
            if (Schema::hasColumn($model->getTable(), 'criadero_id')) {
                $builder->whereIn($model->getTable() . '.criadero_id', $criaderoIds);
            } 
            
            // CASO 2: Caso especial para HorarioAlimentacion (relación indirecta)
            elseif ($model instanceof \App\Models\HorarioAlimentacion) {
                // Usamos whereHas para filtrar basado en la relación 'dispensador'
                $builder->whereHas('dispensador', function ($query) use ($criaderoIds) {
                    $query->whereIn('criadero_id', $criaderoIds);
                });
            }
            
            // Aquí podríamos añadir más casos especiales para otros modelos en el futuro
            
            // --- FIN DE LA LÓGICA INTELIGENTE ---
        }
    }
}