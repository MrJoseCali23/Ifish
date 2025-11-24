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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Criadero::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $dueños = User::where('rol', 'Dueño')->get();

        // Dueño 1 (ID 2) - 1 criadero
        Criadero::create(['nombre' => 'Piscícola El Dorado', 'user_id' => $dueños[0]->id, 'ubicacion' => 'Beni']);
        // Dueño 2 (ID 3) - 1 criadero
        Criadero::create(['nombre' => 'Acuicultura Andina', 'user_id' => $dueños[1]->id, 'ubicacion' => 'La Paz']);
        // Dueño 3 (ID 4) - 2 criaderos
        Criadero::create(['nombre' => 'Granja Titicaca', 'user_id' => $dueños[2]->id, 'ubicacion' => 'Copacabana']);
        Criadero::create(['nombre' => 'Valle Alto Pescado', 'user_id' => $dueños[2]->id, 'ubicacion' => 'Cochabamba']);
        // Dueño 4 (ID 5) - 2 criaderos
        Criadero::create(['nombre' => 'Peces del Oriente', 'user_id' => $dueños[3]->id, 'ubicacion' => 'Santa Cruz']);
        Criadero::create(['nombre' => 'Tambaqui Tropical', 'user_id' => $dueños[3]->id, 'ubicacion' => 'Trinidad']);
        // Dueño 5 (ID 6) - 1 criadero archivado
        Criadero::create(['nombre' => 'Criadero Antiguo', 'user_id' => $dueños[4]->id, 'ubicacion' => 'Sucre', 'estado' => 'Archivado']);

        // Jose Callisaya - 2 criaderos
        $jose = User::where('email', 'jose.c@cliente.com')->first();
        if ($jose) {
            Criadero::create(['nombre' => 'Criadero Principal', 'user_id' => $jose->id, 'ubicacion' => 'Cochabamba']);
            Criadero::create(['nombre' => 'Criadero de Pruebas', 'user_id' => $jose->id, 'ubicacion' => 'Santa Cruz']);
        }
    }
}