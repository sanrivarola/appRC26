<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraLegacy extends Model
{
    protected $table = 'compras_legacy';
    protected $primaryKey = 'id_compras';
    public $timestamps = false;

    protected $fillable = [
        'area_id',
        'proveedor_id',
        'prioridad',
        'producto',
        'cantidad',
        'responsable',
        'creador',
        'estado',
        'archived_at',
        'fechacarga',
        'fechacompra'
    ];

    protected $casts = [
        'fechacarga'  => 'date',
        'fechacompra' => 'date',
        'archived_at' => 'datetime'
    ];

    public function area()
    {
        return $this->belongsTo(AreaInventario::class, 'area_id', 'id_areainventario');
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorAdmin::class, 'proveedor_id', 'id_proveedores');
    }
}
