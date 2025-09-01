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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('criadero_id')
                ->nullable() // Permitimos que sea nulo (importante para nuestro "Super Admin")
                ->after('id') // La colocamos justo después de la columna 'id' por orden
                ->constrained('criaderos') // La vinculamos a la tabla 'criaderos' que creamos
                ->onDelete('cascade'); // Si se borra un criadero, se borran sus usuarios asociados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Para borrar la columna, primero debemos eliminar la restricción de clave foránea
            $table->dropForeign(['criadero_id']);
            // Y luego borramos la columna
            $table->dropColumn('criadero_id');
        });
    }
};
