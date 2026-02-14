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
        Schema::create('dispensador_eventos', function (Blueprint $table) {
            $table->id();

            // A qué dispensador le ocurrió el evento.
            // Si se borra el dispensador, se borra su historial.
            $table->foreignId('dispensador_id')
                  ->references('id_dispensador')->on('Dispensadores')
                  ->cascadeOnDelete();
            
            // El tipo de evento que ocurrió. Usamos string para más flexibilidad.
            $table->string('tipo_evento'); // Ej: 'asignacion_criadero', 'mantenimiento'

            // Una descripción legible del evento para mostrar en la bitácora.
            // Ej: "Asignado a 'Criadero Los Pacuses' por el usuario Admin iFISH".
            $table->text('descripcion'); 

            // El usuario que generó el evento (puede ser nulo si es un evento automático del sistema).
            // Si el usuario se borra, el historial no se ve afectado.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // La fecha en que ocurrió el evento.
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
        Schema::dropIfExists('dispensador_eventos');
    }
};