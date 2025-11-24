<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Criadero;
use App\Models\Dispensador;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Desactivar llaves foráneas y truncar tablas para un reinicio limpio
        Schema::disableForeignKeyConstraints();
        DB::table('registros_alimentacion')->truncate();
        DB::table('horarios_alimentacion')->truncate();
        DB::table('dispensador_eventos')->truncate();
        DB::table('dispensadores')->truncate();
        DB::table('estanques')->truncate();
        DB::table('tipos_comida')->truncate();
        DB::table('criaderos')->truncate();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Ejecutar los seeders base
        $this->call([
            UserSeeder::class,
            CriaderoSeeder::class,
        ]);
        
        // 3. Obtener todos los criaderos y sembrar sus datos de forma aislada
        $dispensadorCounter = 1;
        $criaderos = Criadero::all();
        foreach ($criaderos as $criadero) {
            $this->call(CriaderoDataSeeder::class, false, [
                'criadero' => $criadero,
                'dispensadorCounter' => &$dispensadorCounter
            ]);
        }

        // 4. Crear 4 dispensadores de inventario (sin asignar)
        for ($i = 1; $i <= 4; $i++) {
            Dispensador::create([
                'mac_address' => $this->generateMacAddress(),
                'modelo' => 'Dispensador de Inventario #' . $i,
                'estado' => 'Inactivo', // Los no asignados están inactivos por defecto
                'criadero_id' => null,
                'id_estanque' => null,
            ]);
        }
    }

    private function generateMacAddress()
    {
        do {
            $mac = implode(':', str_split(substr(str_shuffle('0123456789ABCDEF'), 0, 12), 2));
        } while ($mac === 'DC:4F:22:7D:8B:7C' || Dispensador::where('mac_address', $mac)->exists());
        return $mac;
    }
}
