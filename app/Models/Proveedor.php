<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores_admin';
    protected $primaryKey = 'id_proveedores';
    public    $timestamps = false;

    protected $fillable = [
        'area', 'nombre', 'datos', 'producto',
        'responsable', 'detalle', 'creador', 'fechacarga',
    ];
}
