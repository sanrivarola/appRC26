<?php
// database/migrations/2025_06_22_060000_alter_proveedores_admin.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Aseguramos InnoDB
        DB::statement('ALTER TABLE proveedores_admin ENGINE=InnoDB');

        Schema::table('proveedores_admin', function (Blueprint $table) {
            // la columna ya existe → usamos change()
            $table->unsignedInteger('id_proveedores', true)->change();
        });
    }

    public function down(): void
    {
        // no es imprescindible revertir
    }
};
