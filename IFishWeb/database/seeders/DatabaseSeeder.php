<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Criadero;

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
        $criaderos = Criadero::all();
        foreach ($criaderos as $criadero) {
            $this->call(CriaderoDataSeeder::class, false, ['criadero' => $criadero]);
        }
    }
}
