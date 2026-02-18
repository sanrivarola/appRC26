<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relevamientos', function (Blueprint $table) {
            $table->id('id_relevamiento');
            $table->string('equipo', 255);
            $table->string('titulorelevamiento', 255);
            $table->longText('detallerelevamiento');
            $table->string('foto1', 255)->nullable();
            $table->string('archivar', 2)->default('no');   // ‘no’ | ‘si’
            $table->date('fechacarga');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relevamientos');
    }
};
