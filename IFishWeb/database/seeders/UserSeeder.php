<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Usamos truncate para vaciar la tabla antes de llenarla, evitando errores de duplicados.
        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                'name' => 'Admin iFISH',
                'email' => 'admin@ifish.app',
                'password' => Hash::make('password'), // La contraseña será "password"
                'rol' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Ribera',
                'email' => 'carlos.r@ifish.app',
                'password' => Hash::make('password'), // La contraseña también será "password"
                'rol' => 'Trabajador',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}