<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HorarioAlimentacion;
use App\Models\Dispensador;
use App\Models\TipoComida;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        HorarioAlimentacion::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $dispensadores = Dispensador::all();
        $horas = ['08:00:00', '12:30:00', '17:00:00'];

        foreach ($dispensadores as $dispensador) {
            $comida = TipoComida::where('criadero_id', $dispensador->criadero_id)->orWhereNull('criadero_id')->inRandomOrder()->first();
            if ($comida) {
                $dispensador->update(['current_tipo_comida_id' => $comida->id_tipo_comida]);

                foreach ($horas as $hora) {
                    HorarioAlimentacion::create([
                        'id_dispensador' => $dispensador->id_dispensador,
                        'id_tipo_comida' => $comida->id_tipo_comida,
                        'hora_programada' => $hora,
                        'cantidad_gramos' => rand(200, 800),
                        'creado_por_usuario' => $dispensador->criadero->user_id,
                        'activo' => true,
                    ]);
                }
            }
        }
    }
}