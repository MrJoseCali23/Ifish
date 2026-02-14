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
        Schema::table('Dispensadores', function (Blueprint $table) {
            // Guardará la fecha y hora hasta la que el dispensador está activo.
            // Es nullable porque un dispensador en inventario no tiene fecha de expiración.
            $table->timestamp('disponible_hasta')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Dispensadores', function (Blueprint $table) {
            //
        });
    }
};
