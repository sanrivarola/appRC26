<?php
// database/migrations/2025_07_01_000000_create_mantenimiento_devoluciones_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up()
    {
        Schema::create('mantenimiento_devoluciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mantenimiento');
            $table->string('equipo');
            $table->date('fecha');
            $table->text('devolucion');
            $table->timestamps();

            $table
                ->foreign('id_mantenimiento')
                ->references('id_mantenimiento')
                ->on('mantenimientos')
                ->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('mantenimiento_devoluciones');
    }
};
