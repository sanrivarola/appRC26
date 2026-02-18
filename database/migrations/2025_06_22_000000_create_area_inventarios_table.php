<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_inventarios', function (Blueprint $table) {
            $table->bigIncrements('id_areainventario');      // PK
            $table->string('nombre');
            $table->foreignId('creador')                     // FK → users.id
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_inventarios');
    }
};
