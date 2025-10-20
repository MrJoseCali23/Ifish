<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoComida;
use App\Models\Criadero;
use Illuminate\Support\Facades\DB;

class TipoComidaSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TipoComida::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ya no creamos comidas "Globales".
        // El Super Admin no tiene nada que ver con esto.

        // Simplemente creamos un catálogo de ejemplo para cada criadero existente.
        $criaderos = Criadero::all();
        foreach ($criaderos as $criadero) {
            for ($i = 1; $i <= 5; $i++) {
                TipoComida::create([
                    'nombre_comida' => 'Alimento Específico ' . $criadero->nombre . ' #' . $i,
                    'proveedor' => 'Proveedor Local',
                    'criadero_id' => $criadero->id, // Asignado al criadero del dueño
                ]);
            }
        }
    }
}