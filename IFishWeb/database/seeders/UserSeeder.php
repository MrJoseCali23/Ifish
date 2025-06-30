<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema; // <-- AÑADE ESTA IMPORTACIÓN

class UserSeeder extends Seeder
{
    public function run()
    {
        // Desactivamos temporalmente la revisión de claves foráneas
        Schema::disableForeignKeyConstraints();
        
        // Vaciamos la tabla de forma segura
        DB::table('users')->truncate();
        
        // Reactivamos la revisión
        Schema::enableForeignKeyConstraints();

        // Insertamos los usuarios con los datos correctos
        DB::table('users')->insert([
            [
                'name' => 'Admin iFISH',
                'email' => 'admin@ifish.app',
                'password' => Hash::make('password'), // La contraseña es 'password'
                'rol' => 'Admin', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Ribera',
                'email' => 'carlos.r@ifish.app',
                'password' => Hash::make('password'),
                'rol' => 'Trabajador',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}