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
            $table->foreignId('criadero_id')
                ->nullable() // <-- AÑADIMOS ESTO
                ->after('id_dispensador')
                ->constrained('criaderos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('Dispensadores', function (Blueprint $table) {
            $table->dropForeign(['criadero_id']);
            $table->dropColumn('criadero_id');
        });
    }
};
