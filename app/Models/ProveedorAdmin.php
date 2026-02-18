<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedorAdmin extends Model
{
    protected $table      = 'proveedores_admin';   // nombre real de la tabla
    protected $primaryKey = 'id_proveedores';      // clave primaria real
    public    $incrementing = true;                // la PK es auto-incremental
    protected $keyType    = 'int';

    // columnas que podrás asignar en create() / update()
    protected $fillable = [
        'area',
        'nombre',
        'datos',
        'producto',
        'responsable',
        'Detalle',
        'creador',
        'fechacarga',
    ];

    /* --- si vas a relacionar con compras --- */
    public function compras()
    {
        return $this->hasMany(Compra::class,'proveedor_id','id_proveedores');
    }
}
