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
    // .../database/migrations/..._create_estanques_table.php
    public function up(): void
    {
        Schema::create('Estanques', function (Blueprint $table) {
            $table->id('id_estanque');
            $table->string('nombre_estanque', 100);
            $table->string('ubicacion', 255)->nullable();
            $table->string('dimensiones_metros', 50)->nullable();
            $table->foreignId('creado_por_usuario')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->foreignId('actualizado_por_usuario')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estanques');
    }
};
