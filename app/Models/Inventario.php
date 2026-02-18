<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventarios';
    protected $primaryKey = 'id_inventario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'area_id',
        'subareainventario', // ⚠️ revisar si seguís usando este o si migraste a subarea_id
        'subarea_id',
        'nombreproducto',
        'detalle',
        'vencimiento',
        'cantidad',
        'stockminimo',
        'fechacarga',
    ];

    /* ─────────────── Relaciones ─────────────── */

    public function area()
    {
        // Ajustá PK/tabla si difiere en tu esquema real
        return $this->belongsTo(AreaInventario::class, 'area_id', 'id_areainventario');
    }

    public function subarea()
    {
        return $this->belongsTo(SubareaInventario::class, 'subareainventario', 'id_subareasinventario');
    }

    public function historial()
    {
        // FK en historialinventario: producto_id → PK local id_inventario
        return $this->hasMany(HistorialInventario::class, 'producto_id', 'id_inventario')
            ->orderByDesc('fecha')
            ->orderByDesc('id_historial'); // ✅ columna correcta
    }
}
