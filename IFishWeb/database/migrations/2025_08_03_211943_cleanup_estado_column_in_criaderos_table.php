<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Reemplazamos el método de Laravel con una sentencia SQL directa.
        // Esto le ordena a MySQL que modifique la columna, sin intermediarios.
        DB::statement("ALTER TABLE criaderos CHANGE estado estado ENUM('Activo', 'Suspendido', 'Archivado') NOT NULL DEFAULT 'Activo'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Lógica para revertir si fuera necesario
        DB::statement("ALTER TABLE criaderos CHANGE estado estado ENUM('Activo', 'Inactivo', 'Suspendido', 'Archivado') NOT NULL DEFAULT 'Activo'");
    }
};
