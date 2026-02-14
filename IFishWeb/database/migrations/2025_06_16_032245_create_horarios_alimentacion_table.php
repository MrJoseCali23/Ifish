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
    // .../database/migrations/..._create_horarios_alimentacion_table.php

    public function up(): void
    {
        Schema::create('Horarios_Alimentacion', function (Blueprint $table) {
            $table->id('id_horario');

            // --- Claves Foráneas ---
            // Conexión con Dispensadores
            $table->foreignId('id_dispensador')
                ->constrained('Dispensadores', 'id_dispensador')
                ->onDelete('cascade');

            // Conexión con Tipos_Comida
            $table->foreignId('id_tipo_comida')
                ->constrained('Tipos_Comida', 'id_tipo_comida');

            // Conexión con Usuarios (la tabla 'users' de Breeze)
            $table->foreignId('creado_por_usuario')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null');

            // --- Campos Propios de la Tabla ---
            // TU SQL: hora_programada TIME NOT NULL
            $table->time('hora_programada');

            // TU SQL: cantidad_gramos INT NOT NULL
            $table->integer('cantidad_gramos');

            // TU SQL: activo BOOLEAN DEFAULT TRUE
            $table->boolean('activo')->default(true);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horarios_alimentacion');
    }
};
