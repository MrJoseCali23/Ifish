<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En lugar de Schema::table, usamos una sentencia SQL directa
        // Esto le dice a MySQL que modifique la columna 'rol' para aceptar los nuevos valores.
        DB::statement("ALTER TABLE users CHANGE rol rol ENUM('Admin', 'Dueño', 'Trabajador') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
