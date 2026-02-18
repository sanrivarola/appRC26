<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DevolucionTarea;  

class Tarea extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'id_tarea';
    public $timestamps = false;

    protected $fillable = [
        'creador', 'tarea', 'detalle', 'estado', 'imagen', 'equipo',
        'fecha_carga', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo',
        'norepite', 'horacomienzo', 'horafinal', 'prioridad', 'link1', 'link2', 'link3','fecha_carga'
    ];

    public function devoluciones()
    {
        return $this->hasMany(DevolucionTarea::class, 'id_tarea', 'id_tarea');
    }
    protected $casts = [
    'fecha_carga' => 'date',
    ];

}
