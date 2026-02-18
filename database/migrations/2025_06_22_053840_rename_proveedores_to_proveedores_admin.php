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
        Schema::rename('proveedores', 'proveedores_admin');

        Schema::table('proveedores_admin', function (Blueprint $table) {
            $table->bigInteger('id_proveedores', true, true)->change(); // asegura PK
        });
    }

    public function down(): void
    {
        Schema::rename('proveedores_admin', 'proveedores');
    }

};
