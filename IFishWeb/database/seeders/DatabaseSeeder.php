<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            // Aquí puedes añadir los otros seeders que creaste,
            // como TipoComidaSeeder::class, EstanqueSeeder::class, etc.
        ]);
    }
}