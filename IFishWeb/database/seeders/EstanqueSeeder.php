<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estanque;
use App\Models\Criadero;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EstanqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Desactivamos temporalmente las restricciones de clave foránea para poder truncar la tabla
        Schema::disableForeignKeyConstraints();
        Estanque::truncate();
        Schema::enableForeignKeyConstraints();

        // Obtenemos todos los criaderos que hemos creado
        $criaderos = Criadero::all();

        // Para cada criadero, creamos 5 estanques de ejemplo
        foreach ($criaderos as $criadero) {
            for ($i = 1; $i <= 5; $i++) {
                Estanque::create([
                    'nombre_estanque' => 'Estanque ' . chr(64 + $i), // Genera nombres como Estanque A, Estanque B, etc.
                    'criadero_id' => $criadero->id,
                    'ubicacion' => 'Sector ' . $i,
                    'dimensiones_metros' => rand(10, 50) . 'x' . rand(5, 25) . 'm',
                    'creado_por_usuario' => $criadero->user_id, // El estanque es creado por el dueño del criadero
                ]);
            }
        }
    }
}