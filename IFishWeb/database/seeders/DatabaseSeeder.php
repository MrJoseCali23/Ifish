<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CriaderoSeeder::class,
            EstanqueSeeder::class,
            TipoComidaSeeder::class,
            DispensadorSeeder::class,
            HorarioAlimentacionSeeder::class,
        ]);
    }
}