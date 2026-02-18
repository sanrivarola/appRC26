<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores_admin', function (Blueprint $table) {
            $table->bigIncrements('id_proveedores');   // PK correcta
            $table->string('area');
            $table->string('nombre');
            $table->mediumText('datos');
            $table->mediumText('producto')->nullable();
            $table->string('responsable');
            $table->longText('detalle');
            $table->string('creador');
            $table->date('fechacarga');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores_admin');
    }
};
