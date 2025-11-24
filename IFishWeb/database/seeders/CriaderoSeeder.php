<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Criadero;
use Illuminate\Support\Facades\DB;

class CriaderoSeeder extends Seeder
{
    public function run()
    {
        // Get specific users
        $jorge = User::where('email', 'jorge.a@cliente.com')->first();
        $ana = User::where('email', 'ana.q@cliente.com')->first();
        $carlos = User::where('email', 'carlos.m@cliente.com')->first();
        $jose = User::where('email', 'jose.c@cliente.com')->first();

        // Jorge Andrade - 1 criadero
        if ($jorge) {
            Criadero::create(['nombre' => 'Piscícola del Sur', 'user_id' => $jorge->id, 'ubicacion' => 'Tarija']);
        }

        // Ana Maria Quiroga - 2 criaderos
        if ($ana) {
            Criadero::create(['nombre' => 'Acuicultura Andina', 'user_id' => $ana->id, 'ubicacion' => 'La Paz']);
            Criadero::create(['nombre' => 'Truchas del Lago', 'user_id' => $ana->id, 'ubicacion' => 'Puno']);
        }

        // Carlos Mendoza - 2 criaderos
        if ($carlos) {
            Criadero::create(['nombre' => 'Valle Alto Pescado', 'user_id' => $carlos->id, 'ubicacion' => 'Cochabamba']);
            Criadero::create(['nombre' => 'Peces del Oriente', 'user_id' => $carlos->id, 'ubicacion' => 'Santa Cruz']);
        }

        // Jose Callisaya - 2 criaderos
        if ($jose) {
            Criadero::create(['nombre' => 'Criadero Principal', 'user_id' => $jose->id, 'ubicacion' => 'Cochabamba']);
            Criadero::create(['nombre' => 'Criadero de Pruebas', 'user_id' => $jose->id, 'ubicacion' => 'Santa Cruz']);
        }
    }
}