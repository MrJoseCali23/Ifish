<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Hacemos que la contraseña sea opcional (nullable)
            // para poder crear usuarios que aún no la han establecido.
            $table->string('password')->nullable()->change();

            // Añadimos las nuevas columnas para el sistema de invitación
            $table->string('invitation_token')->unique()->nullable()->after('remember_token');
            $table->timestamp('invitation_expires_at')->nullable()->after('invitation_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Lógica para revertir los cambios
            $table->string('password')->nullable(false)->change();
            $table->dropColumn(['invitation_token', 'invitation_expires_at']);
        });
    }
};
