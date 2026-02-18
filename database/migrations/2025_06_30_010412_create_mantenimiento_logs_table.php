<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mantenimiento_logs', function(Blueprint $t){
        $t->id();
        $t->foreignId('mantenimiento_id')
            ->constrained('mantenimientos','id_mantenimiento');
        $t->foreignId('user_id')->constrained()->onDelete('cascade');
        $t->text('nota');
        $t->timestamp('created_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_logs');
    }
};
