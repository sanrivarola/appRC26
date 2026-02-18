<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1 · Si ya existe `proveedores_admin` => la copiamos a un backup
        if (Schema::hasTable('proveedores_admin')) {
            // nombre alternativo para no perder nada
            Schema::rename('proveedores_admin', 'proveedores_admin_backup');
        }

        // 2 · Si existe la tabla original => la renombramos
        if (Schema::hasTable('proveedores')
            && !Schema::hasTable('proveedores_admin')) {

            Schema::rename('proveedores', 'proveedores_admin');
        }

        // 3 · ( Opcional ) Mezclamos los datos que hubiera en el backup
        if (Schema::hasTable('proveedores_admin_backup')) {
            DB::table('proveedores_admin_backup')
                ->get()
                ->each(fn ($row) =>
                    DB::table('proveedores_admin')->updateOrInsert(
                        ['id_proveedores' => $row->id_proveedores],
                        (array) $row
                    )
                );

            // Si todo salió bien ya puedes eliminar el backup:
            // Schema::drop('proveedores_admin_backup');
        }
    }

    public function down(): void
    {
        // Des-hace la operación de forma segura
        if (Schema::hasTable('proveedores_admin')
            && !Schema::hasTable('proveedores')) {

            Schema::rename('proveedores_admin', 'proveedores');
        }

        if (Schema::hasTable('proveedores_admin_backup')
            && !Schema::hasTable('proveedores_admin')) {

            Schema::rename('proveedores_admin_backup', 'proveedores_admin');
        }
    }
};
