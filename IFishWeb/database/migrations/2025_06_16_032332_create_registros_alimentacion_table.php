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
    // .../database/migrations/..._create_registros_alimentacion_table.php

    public function up(): void
    {
        Schema::create('Registros_Alimentacion', function (Blueprint $table) {
            $table->id('id_registro');

            // --- Claves Foráneas ---
            // Se deja el comportamiento por defecto (restrict) para evitar borrar un dispensador si tiene registros.
            $table->foreignId('id_dispensador')->constrained('Dispensadores', 'id_dispensador');

            $table->foreignId('id_tipo_comida')->nullable()->constrained('Tipos_Comida', 'id_tipo_comida')->onDelete('set null');

            $table->foreignId('iniciado_por_usuario')->nullable()->constrained('users', 'id')->onDelete('set null');

            // --- Campos Propios ---
            // TU SQL: cantidad_dispensada_gramos INT NOT NULL
            $table->integer('cantidad_dispensada_gramos');

            // TU SQL: tipo_alimentacion ENUM('Programada', 'Manual') NOT NULL
            $table->enum('tipo_alimentacion', ['Programada', 'Manual']);

            // TU SQL: exitoso BOOLEAN
            $table->boolean('exitoso')->nullable();

            // TU SQL: fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            // Usamos timestamps() que crea created_at y updated_at. Usaremos created_at para este propósito.
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
        Schema::dropIfExists('registros_alimentacion');
    }
};
