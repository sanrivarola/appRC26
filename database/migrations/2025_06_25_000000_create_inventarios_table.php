<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->bigIncrements('id_inventario');

            // Relación a área y subárea (guardamos ID, no texto)
            $table->unsignedBigInteger('areainventario');
            $table->unsignedBigInteger('subareainventario');

            $table->string('nombreproducto');
            $table->mediumText('detalle')->nullable();
            $table->date('vencimiento')->nullable();
            $table->integer('cantidad')->default(0);
            $table->integer('stockminimo')->default(1);
            $table->date('fechacarga')->default(now());

            $table->foreign('areainventario')
                  ->references('id_areainventario')
                  ->on('area_inventarios')
                  ->cascadeOnDelete();

            $table->foreign('subareainventario')
                  ->references('id_subareasinventario')
                  ->on('inventario_subareas')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
