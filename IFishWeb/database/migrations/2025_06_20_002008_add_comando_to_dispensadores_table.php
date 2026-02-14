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
        Schema::table('Dispensadores', function (Blueprint $table) {
            $table->string('comando_pendiente')->nullable()->after('ultimo_reporte');
            $table->integer('comando_valor')->nullable()->after('comando_pendiente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensadores', function (Blueprint $table) {
            //
        });
    }
};
