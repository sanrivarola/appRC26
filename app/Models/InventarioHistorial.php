<?php
// app/Models/InventarioHistorial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioHistorial extends Model
{
    // 1) Nombre de la tabla
    protected $table = 'historialinventario';

    // 2) Indica que la PK no es "id" sino "id_historial"
    protected $primaryKey = 'id_historial';

    // 3) Deshabilita timestamps automáticos si no tienes created_at/updated_at
    public $timestamps = false;

    // 4) Columnas llenables
    protected $fillable = [
      'producto_id',
      'tipo_operacion',
      'cantidad_operacion',
      'equipo',
      'fecha',
    ];

    // 5) Relación inversa (opcional)
    public function producto()
    {
        return $this->belongsTo(
            \App\Models\Inventario::class,
            'producto_id',
            'id_inventario'
        );
    }
}
