<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <-- Añadido
class EstanqueSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints(); // <-- Añadido
        DB::table('estanques')->truncate();
        Schema::enableForeignKeyConstraints(); // <-- Añadido

        DB::table('estanques')->insert([
            [ 'nombre_estanque' => 'Estanque Principal A-01', 'criadero_id' => 1, 'ubicacion' => 'Sector Norte', 'dimensiones_metros' => '20x10x2', 'creado_por_usuario' => 2, 'actualizado_por_usuario' => 2, 'created_at' => now(),'updated_at' => now() ],
            [ 'nombre_estanque' => 'Estanque de Cría B-02', 'criadero_id' => 1, 'ubicacion' => 'Sector Sur', 'dimensiones_metros' => '5x5x1.5', 'creado_por_usuario' => 2, 'actualizado_por_usuario' => 2, 'created_at' => now(),'updated_at' => now() ],
        ]);
    }
}