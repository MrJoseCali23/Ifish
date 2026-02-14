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
    Schema::table('Tipos_Comida', function (Blueprint $table) {
        $table->dropUnique('tipos_comida_nombre_comida_unique'); // Borra la regla antigua
        $table->unique(['nombre_comida', 'criadero_id']); // Crea la nueva regla compuesta
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Tipos_Comida', function (Blueprint $table) {
            //
        });
    }
};
