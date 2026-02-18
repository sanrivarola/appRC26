<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo la creamos si no existe todavía
        if (Schema::hasTable('tareas_admin')) {
            return;
        }

Schema::create('tareas_admin', function (Blueprint $table) {
    $table->bigIncrements('id_tarea_admin');

    // ‼️ MISMA definición que la llave primaria a la que apuntas
    $table->unsignedInteger('proveedor_id');

    $table->string('creador');
    $table->string('tarea');
    $table->mediumText('detalle')->nullable();
    $table->date('fecha_limite')->nullable();
    $table->enum('prioridad', ['Alta','Media','Baja'])->default('Media');
    $table->boolean('realizada')->default(false);
    $table->timestamps();

    // FK
    $table->foreign('proveedor_id')
        ->references('id_proveedores')
        ->on('proveedores_admin')
        ->onDelete('cascade');

});
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas_admin');
    }
};
