<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Super Admin
        User::create([
            'name' => 'Admin iFISH',
            'email' => 'admin@ifish.app',
            'password' => Hash::make('password'),
            'rol' => 'Admin',
            'estado' => 'Activo',
        ]);

        // 2. Dueños Activos
        User::create(['name' => 'Roberto Suarez', 'email' => 'roberto.s@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño']);
        User::create(['name' => 'Ana Maria Quiroga', 'email' => 'ana.q@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño']);
        User::create(['name' => 'Carlos Mendoza', 'email' => 'carlos.m@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño']);
        User::create(['name' => 'Sofia Villarroel', 'email' => 'sofia.v@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño']);
        User::create(['name' => 'Jose Callisaya', 'email' => 'jose.c@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño', 'estado' => 'Activo']);

        // 3. Dueño Inactivo
        User::create(['name' => 'Jorge Andrade', 'email' => 'jorge.a@cliente-inactivo.com', 'password' => Hash::make('password'), 'rol' => 'Dueño', 'estado' => 'Inactivo']);
    }
}
