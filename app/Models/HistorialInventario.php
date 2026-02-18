<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    protected $table = 'historialinventario';

    protected $primaryKey = 'id_historial';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'tipo_operacion',
        'cantidad_operacion',
        'equipo',
        'fecha',
    ];
}
