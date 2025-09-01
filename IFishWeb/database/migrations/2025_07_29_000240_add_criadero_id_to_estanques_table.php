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
        Schema::table('Estanques', function (Blueprint $table) {
            // Añadimos la nueva columna para la relación.
            // Un estanque siempre debe pertenecer a un criadero, por lo que no es nullable.
            $table->foreignId('criadero_id')
                ->after('id_estanque')
                ->constrained('criaderos')
                ->onDelete('cascade'); // Si se borra un criadero, se borran sus estanques
        });
    }

    public function down(): void
    {
        Schema::table('Estanques', function (Blueprint $table) {
            $table->dropForeign(['criadero_id']);
            $table->dropColumn('criadero_id');
        });
    }
};
