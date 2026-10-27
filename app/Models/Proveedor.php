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

    public function tareasProveedores()
    {
        return $this->hasMany(\App\Models\TareaProveedor::class, 'proveedor_id', 'id_proveedores');
    }

    public function calificacionPromedio(): ?float
    {
        return $this->tareasProveedores()
            ->whereNotNull('calificacion')
            ->avg('calificacion');
    }
}
