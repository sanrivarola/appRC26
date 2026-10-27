<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TareaProveedor extends Model
{
    protected $table      = 'tareas_proveedores';
    protected $primaryKey = 'id';

    protected $fillable = [
        'proveedor_id', 'creador', 'titulo', 'detalle',
        'tipo', 'prioridad', 'estado', 'resultado',
        'calificacion', 'fecha_carga', 'fecha_limite', 'fecha_cierre',
    ];

    protected $casts = [
        'fecha_carga'  => 'date',
        'fecha_limite' => 'date',
        'fecha_cierre' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'id_proveedores');
    }

    public function devoluciones()
    {
        return $this->hasMany(DevolucionTareaProveedor::class, 'tarea_id')->latest();
    }

    // Helpers de color para vistas
    public function colorEstado(): string
    {
        return match ($this->estado) {
            'Pendiente' => 'bg-yellow-100 text-yellow-800',
            'En curso'  => 'bg-blue-100 text-blue-800',
            'Cerrada'   => 'bg-slate-100 text-slate-600',
            default     => 'bg-slate-100 text-slate-600',
        };
    }

    public function colorResultado(): string
    {
        return match ($this->resultado) {
            'Resuelto'               => 'bg-green-100 text-green-800',
            'Parcialmente resuelto'  => 'bg-amber-100 text-amber-800',
            'Sin respuesta'          => 'bg-red-100 text-red-700',
            'Pendiente'              => 'bg-yellow-100 text-yellow-800',
            default                  => 'bg-slate-100 text-slate-500',
        };
    }

    public function colorTipo(): string
    {
        return match ($this->tipo) {
            'Reclamo'    => 'bg-red-100 text-red-700',
            'Consulta'   => 'bg-sky-100 text-sky-700',
            'Seguimiento'=> 'bg-violet-100 text-violet-700',
            'Renovación' => 'bg-emerald-100 text-emerald-700',
            default      => 'bg-slate-100 text-slate-600',
        };
    }
}
