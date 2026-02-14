<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Dispensadores', function (Blueprint $table) {
            // Añadimos la nueva columna para guardar el tipo de comida actual
            $table->foreignId('current_tipo_comida_id')
                  ->nullable() // Puede no tener comida asignada (estar vacío)
                  ->after('estado') // La colocamos después de la columna 'estado' por orden
                  ->constrained('Tipos_Comida', 'id_tipo_comida') // Apunta a la tabla y columna correctas
                  ->nullOnDelete(); // Si se borra el tipo de comida, este campo se pone nulo
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Dispensadores', function (Blueprint $table) {
            $table->dropForeign(['current_tipo_comida_id']);
            $table->dropColumn('current_tipo_comida_id');
        });
    }
};