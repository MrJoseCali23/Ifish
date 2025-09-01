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
    // .../database/migrations/..._create_dispensadores_table.php

    public function up(): void
    {
        Schema::create('Dispensadores', function (Blueprint $table) {
            // TU SQL: id_dispensador INT PRIMARY KEY AUTO_INCREMENT
            $table->id('id_dispensador');

            // TU SQL: FOREIGN KEY (id_estanque) REFERENCES Estanques(id_estanque) ON DELETE CASCADE
            $table->foreignId('id_estanque')
                ->nullable()
                ->constrained('Estanques', 'id_estanque')
                ->onDelete('set null');

            // TU SQL: mac_address VARCHAR(17) UNIQUE NOT NULL
            $table->string('mac_address', 17)->unique();

            // TU SQL: modelo VARCHAR(50)
            $table->string('modelo', 50)->nullable();

            // TU SQL: estado ENUM('Activo', 'Inactivo', 'Error') DEFAULT 'Activo'
            $table->enum('estado', ['Activo', 'Inactivo', 'Error'])->default('Activo');

            // TU SQL: nivel_comida_actual_kg DECIMAL(6, 2) DEFAULT 0.00
            $table->decimal('nivel_comida_actual_kg', 8, 2)->default(0.00);

            // TU SQL: ultimo_reporte TIMESTAMP
            $table->timestamp('ultimo_reporte')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dispensadores');
    }
};
