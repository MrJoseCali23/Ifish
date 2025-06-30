<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin iFISH',
                'email' => 'admin@ifish.app',
                'password' => Hash::make('123456'), // La contraseña será "password"
                'rol' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Ribera',
                'email' => 'carlos@ifish.app',
                'password' => Hash::make('123456'), // La contraseña será "password"
                'rol' => 'Trabajador',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}