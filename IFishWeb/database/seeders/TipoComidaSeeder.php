<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <-- Añadido
class TipoComidaSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints(); // <-- Añadido
        DB::table('Tipos_comida')->truncate();
        Schema::enableForeignKeyConstraints(); // <-- Añadido

        DB::table('Tipos_comida')->insert([
            [ 'nombre_comida' => 'iFeed Crecimiento 3mm', 'descripcion' => 'Pellets de 3mm', 'proveedor' => 'NutriPez S.A.', 'criadero_id' => 1 ],
            [ 'nombre_comida' => 'iFeed Engorde 5mm', 'descripcion' => 'Pellets de 5mm', 'proveedor' => 'AquaFeed', 'criadero_id' => 1 ],
        ]);
    }
}