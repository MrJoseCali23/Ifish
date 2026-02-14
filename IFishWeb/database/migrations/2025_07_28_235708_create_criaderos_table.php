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
        Schema::create('criaderos', function (Blueprint $table) {
            $table->id(); // ID único para cada criadero
            $table->string('nombre'); // Nombre del criadero, ej: "Piscícola El Dorado"

            // Relación con el usuario "dueño" del criadero.
            // Si se borra el usuario, se borra el criadero.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 

            $table->string('ubicacion')->nullable(); // Ubicación geográfica del criadero
            $table->enum('estado', ['Activo', 'Inactivo', 'Suspendido'])->default('Activo');
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criaderos');
    }
};