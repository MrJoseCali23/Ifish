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
    // .../database/migrations/..._create_tipos_comida_table.php
    public function up(): void
    {
            Schema::create('Tipos_Comida', function (Blueprint $table) {
                $table->id('id_tipo_comida');
                $table->string('nombre_comida', 100)->unique();
                $table->text('descripcion')->nullable();
                $table->string('proveedor', 100)->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_comida');
    }
};
