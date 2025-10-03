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
            $table->dropForeign(['criadero_id']);
            
            $table->dropColumn('criadero_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Esta es la lógica inversa para poder deshacer el cambio
            $table->foreignId('criadero_id')->nullable()->after('id')->constrained('criaderos')->nullOnDelete();
        });
    }
};