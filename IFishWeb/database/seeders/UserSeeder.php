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
        // 1. Super Admin
        User::create([
            'name' => 'Admin iFISH',
            'email' => 'admin@ifish.app',
            'password' => Hash::make('password'),
            'rol' => 'Admin',
            'estado' => 'Activo',
        ]);

        // 2. Dueños
        User::create(['name' => 'Jorge Andrade', 'email' => 'jorge.a@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño', 'estado' => 'Activo']);
        User::create(['name' => 'Ana Maria Quiroga', 'email' => 'ana.q@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño', 'estado' => 'Activo']);
        User::create(['name' => 'Carlos Mendoza', 'email' => 'carlos.m@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño', 'estado' => 'Activo']);
        User::create(['name' => 'Jose Callisaya', 'email' => 'jose.c@cliente.com', 'password' => Hash::make('password'), 'rol' => 'Dueño', 'estado' => 'Activo']);
    }
}
