<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dispensador;
use App\Models\Estanque;
use Illuminate\Support\Facades\DB;

class DispensadorSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Dispensador::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $estanques = Estanque::all();
        $modelos = ['iDispenser V1', 'iDispenser V2', 'AquaFeed Pro', 'iDispenser V3'];

        for ($i = 0; $i < 20; $i++) {
            $estanque = $estanques->random();
            Dispensador::create([
                'mac_address' => $this->generateMacAddress(),
                'modelo' => $modelos[array_rand($modelos)],
                'estado' => 'Activo',
                'criadero_id' => $estanque->criadero_id,
                'id_estanque' => $estanque->id_estanque,
            ]);
        }
    }

    private function generateMacAddress()
    {
        return implode(':', str_split(substr(str_shuffle('0123456789ABCDEF'), 0, 12), 2));
    }
}