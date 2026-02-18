<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivoFieldsToMantenimientosTable extends Migration
{
    public function up()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->text('motivo_archivo')->nullable()->after('descripcion');
            $table->date('fecha_archivo')->nullable()->after('motivo_archivo');
            // Opcional: si quieres un flag booleano
            $table->boolean('archivado')->default(false)->after('estado');
        });
    }

    public function down()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropColumn(['motivo_archivo','fecha_archivo','archivado']);
        });
    }
}
