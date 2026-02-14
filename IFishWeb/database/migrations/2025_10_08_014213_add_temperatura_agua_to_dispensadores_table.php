<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Dispensadores', function (Blueprint $table) {
            // Añadimos únicamente la columna que falta para el reporte del sensor de temperatura
            $table->decimal('temperatura_agua', 5, 2)->nullable()->after('nivel_comida_actual_kg');
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
            // Lógica para eliminar la columna si necesitamos revertir el cambio
            $table->dropColumn('temperatura_agua');
        });
    }
};
