<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <-- Añadido
class DispensadorSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints(); // <-- Añadido
        DB::table('Dispensadores')->truncate();
        Schema::enableForeignKeyConstraints(); // <-- Añadido

        DB::table('Dispensadores')->insert([
            [ 'id_estanque' => 1, 'criadero_id' => 1, 'mac_address' => 'dc:4f:22:7d:8b:7c', 'modelo' => 'iDispenser V2', 'estado' => 'Activo', 'nivel_comida_actual_kg' => 15.50 ],
            [ 'id_estanque' => 2, 'criadero_id' => 1, 'mac_address' => '3C:71:BF:F1:E6:E2', 'modelo' => 'iDispenser V1', 'estado' => 'Inactivo', 'nivel_comida_actual_kg' => 20.00 ],
        ]);
    }
}