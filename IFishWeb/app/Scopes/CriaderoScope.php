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

            // CASO 1: Caso especial para TipoComida (Debe ver las suyas Y las globales)
            if ($model instanceof \App\Models\TipoComida) {
                $builder->where(function ($query) use ($criaderoIds) {
                    $query->whereIn('criadero_id', $criaderoIds) // Las que pertenecen a sus criaderos
                          ->orWhereNull('criadero_id');       // O las que son globales
                });
            }
            
            // CASO 2: Caso especial para HorarioAlimentacion (relación indirecta)
            elseif ($model instanceof \App\Models\HorarioAlimentacion) {
                $builder->whereHas('dispensador', function ($query) use ($criaderoIds) {
                    $query->whereIn('criadero_id', $criaderoIds);
                });
            }
            
            // CASO 3: El modelo tiene una columna 'criadero_id' directa (Estanques, Dispensadores)
            elseif (Schema::hasColumn($model->getTable(), 'criadero_id')) {
                $builder->whereIn($model->getTable() . '.criadero_id', $criaderoIds);
            }
            
            // --- FIN DE LA LÓGICA INTELIGENTE ---
        }
    }
}