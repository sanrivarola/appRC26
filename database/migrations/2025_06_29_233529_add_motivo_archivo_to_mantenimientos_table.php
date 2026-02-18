<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotivoArchivoToMantenimientosTable extends Migration
{
    public function up()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            // ← use 'descripcion', not 'detalle'
            $table->text('motivo_archivo')->nullable()->after('descripcion');
        });
    }

    public function down()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropColumn('motivo_archivo');
        });
    }
}
