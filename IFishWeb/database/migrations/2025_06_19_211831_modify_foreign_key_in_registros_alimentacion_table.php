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
    public function up(): void
    {
        Schema::table('Registros_Alimentacion', function (Blueprint $table) {
            // 1. Borramos la clave foránea existente.
            // El nombre 'registros_alimentacion_id_dispensador_foreign' lo sacamos del mensaje de error que nos diste.
            $table->dropForeign('registros_alimentacion_id_dispensador_foreign');

            // 2. Volvemos a añadir la clave foránea, pero esta vez con la regla ON DELETE CASCADE.
            $table->foreign('id_dispensador')
                ->references('id_dispensador')
                ->on('Dispensadores')
                ->cascadeOnDelete(); // <-- La nueva regla mágica
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registros_alimentacion', function (Blueprint $table) {
            //
        });
    }
};
