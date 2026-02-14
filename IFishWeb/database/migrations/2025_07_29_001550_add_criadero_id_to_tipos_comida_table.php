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
            $table->foreignId('criadero_id')
                ->nullable() // Permitimos que sea nulo para comidas "globales"
                ->after('id_tipo_comida')
                ->constrained('criaderos')
                ->onDelete('cascade'); // Si se borra un criadero, se borran sus comidas personalizadas
        });
    }

    public function down(): void
    {
        Schema::table('Tipos_Comida', function (Blueprint $table) {
            $table->dropForeign(['criadero_id']);
            $table->dropColumn('criadero_id');
        });
    }
};
