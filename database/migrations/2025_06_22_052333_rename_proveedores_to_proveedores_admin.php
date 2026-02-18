<?php
// database/migrations/2025_06_22_061000_rename_proveedores_to_proveedores_admin.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('proveedores', 'proveedores_admin');
    }

    public function down(): void
    {
        Schema::rename('proveedores_admin', 'proveedores');
    }
};
