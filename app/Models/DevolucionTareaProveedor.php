<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionTareaProveedor extends Model
{
    protected $table      = 'devoluciones_tareas_proveedores';
    protected $primaryKey = 'id';

    protected $fillable = ['tarea_id', 'user_id', 'devolucion'];

    public function tarea()
    {
        return $this->belongsTo(TareaProveedor::class, 'tarea_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
