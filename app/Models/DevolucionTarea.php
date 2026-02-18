<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionTarea extends Model
{
    protected $table = 'devoluciontareas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_tarea',
        'finalizada',
        'equipo',
        'devolucion',
        'fecha',
    ];
}