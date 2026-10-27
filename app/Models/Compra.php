<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    /*--------------------------------------------------------------
    | CONFIGURACIÓN BÁSICA
    --------------------------------------------------------------*/
    protected $table      = 'compras';
    protected $primaryKey = 'id_compras';
    public $incrementing  = true;     // recomendado (tu PK es AI)
    protected $keyType    = 'int';    // recomendado

    /*--------------------------------------------------------------
    | La tabla NO tiene created_at / updated_at
    --------------------------------------------------------------*/
    public $timestamps = false;

    /*--------------------------------------------------------------
    | Route Model Binding
    | (Opcional: Eloquent ya usaría primaryKey por defecto)
    --------------------------------------------------------------*/
    // public function getRouteKeyName(): string
    // {
    //     return 'id_compras';
    // }

    /*--------------------------------------------------------------
    | ASIGNACIÓN MASIVA
    --------------------------------------------------------------*/
    protected $fillable = [
        'area_id',
        'subarea_id',
        'proveedor_id',
        'prioridad',
        'producto',
        'cantidad',
        'responsable',
        'creador',
        'estado',
        'fechacarga',
        'fechacompra',
        'archived_at',
        'motivo_cancelacion',
    ];

    /*--------------------------------------------------------------
    | CASTS
    --------------------------------------------------------------*/
    protected $casts = [
        'fechacarga'  => 'date',
        'fechacompra' => 'date',
        'archived_at' => 'datetime',   // ⬅️ útil para comparar/mostrar
    ];

    /*--------------------------------------------------------------
    | RELACIONES
    --------------------------------------------------------------*/
    public function area()
    {
        return $this->belongsTo(
            AreaInventario::class,
            'area_id',
            'id_areainventario'
        );
    }

    public function subarea()
    {
        return $this->belongsTo(
            SubareaInventario::class,
            'subarea_id',
            'id_subareasinventario'
        );
    }

    public function proveedor()
    {
        return $this->belongsTo(
            ProveedorAdmin::class,
            'proveedor_id',
            'id_proveedores'
        );
    }

    public function movimientos()
    {
        // foreignKey = compra_id ; localKey por defecto = primaryKey (id_compras)
        return $this->hasMany(CompraMovimiento::class, 'compra_id')
                    ->latest(); // ordena por created_at desc
    }
}