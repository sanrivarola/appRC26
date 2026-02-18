<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_subareas', function (Blueprint $table) {
            $table->bigIncrements('id_subareasinventario');      // PK
            $table->string('nombre');
            $table->unsignedBigInteger('areapadre');             // FK → area_inventarios.id_areainventario

            $table->foreign('areapadre')
                  ->references('id_areainventario')
                  ->on('area_inventarios')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_subareas');
    }
};
