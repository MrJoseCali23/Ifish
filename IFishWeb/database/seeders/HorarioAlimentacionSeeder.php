<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class HorarioAlimentacionSeeder extends Seeder
{
    public function run()
    {
        DB::table('Horarios_alimentacion')->truncate();
        DB::table('Horarios_alimentacion')->insert([
            [ 'id_dispensador' => 1, 'id_tipo_comida' => 1, 'creado_por_usuario' => 2, 'hora_programada' => '08:00:00', 'cantidad_gramos' => 500, 'activo' => 1 ],
            [ 'id_dispensador' => 1, 'id_tipo_comida' => 2, 'creado_por_usuario' => 2, 'hora_programada' => '16:00:00', 'cantidad_gramos' => 750, 'activo' => 1 ],
        ]);
    }
}