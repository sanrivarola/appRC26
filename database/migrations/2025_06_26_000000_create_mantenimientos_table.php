<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMantenimientosTable extends Migration
{
    public function up()
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();                             // id
            $table->string('tipo');                   // tipo de mantenimiento (e.g. Tractor)
            $table->string('equipo');                 // responsable en campo
            $table->text('responsable');              // datos de contacto
            $table->text('tiempo');                   // frecuencia
            $table->text('detalle');                  // descripción
            $table->tinyInteger('estado')->default(1);// 1=activo,0=inactivo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mantenimientos');
    }
}
