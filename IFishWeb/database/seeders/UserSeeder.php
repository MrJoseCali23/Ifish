<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('users')->insert([
            [
                'name' => 'Admin iFISH', 'email' => 'admin@ifish.app',
                'password' => Hash::make('password'), 'rol' => 'Admin',
                'criadero_id' => null, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Ribera', 'email' => 'carlos.r@ifish.app',
                'password' => Hash::make('password'), 'rol' => 'Dueño', // Ahora es un Dueño
                'criadero_id' => null, 'created_at' => now(), 'updated_at' => now(),
            ]
        ]);
    }
}