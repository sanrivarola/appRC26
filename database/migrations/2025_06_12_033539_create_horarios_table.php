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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id('id_horario');
            $table->unsignedBigInteger('user_id');
            $table->date('dia');
            $table->timestamp('horarioentrada')->nullable();
            $table->timestamp('horariosalida')->nullable();
            $table->boolean('ausente')->default(false);
            $table->string('equipo')->nullable(); // Puedes asociar esto al usuario si querés
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
