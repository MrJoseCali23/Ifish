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
    // .../database/migrations/..._create_registros_sensores_table.php

    public function up(): void
    {
        Schema::create('Registros_Sensores', function (Blueprint $table) {
            // TU SQL: id_registro_sensor BIGINT PRIMARY KEY AUTO_INCREMENT
            // El método id() de Laravel crea un BIGINT por defecto, ¡perfecto!
            $table->id('id_registro_sensor');

            // Conexión con Dispensadores
            $table->foreignId('id_dispensador')
                ->constrained('Dispensadores', 'id_dispensador')
                ->onDelete('cascade');

            // --- Campos Propios ---
            $table->string('tipo_sensor', 50);
            $table->string('valor', 50);
            $table->string('unidad_medida', 20)->nullable();

            // Usamos timestamps() y la columna created_at funcionará como fecha_hora
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
        Schema::dropIfExists('registros_sensores');
    }
};
