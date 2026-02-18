<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MantenimientoDevolucion;
use App\Models\User;
use Carbon\Carbon;

class Mantenimiento extends Model
{
    use HasFactory;

    // Si tu tabla no sigue la convención plural de Laravel, descomenta y ajusta:
    // protected $table = 'mantenimientos';

    // PK personalizada
    protected $primaryKey = 'id_mantenimiento';
    public $incrementing = true;
    protected $keyType = 'int';

    // Campos que pueden asignarse masivamente
    protected $fillable = [
        'tipo',
        'responsable',
        'frequency_value',
        'frequency_unit',
        'tiempo',
        'descripcion',
        'equipo',
        'estado',
        'next_due_at',
        'archivado',
        'motivo_archivo',
        'fecha_archivo',
        'creador',
        'fechacarga',
    ];

    // Casts para fechas y booleanos
    protected $casts = [
        'fechacarga'    => 'date',
        'next_due_at'   => 'date',
        'fecha_archivo' => 'date',
        'archivado'     => 'boolean',
        'estado'        => 'boolean',
        'frequency_value' => 'integer',
    ];

    /**
     * El usuario (equipo) al que está asignado este mantenimiento.
     * Mapea la columna 'equipo' (string con el name) al campo 'name' de users.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'equipo', 'name');
    }

    /**
     * Devoluciones asociadas a este mantenimiento, ordenadas de más reciente a más antigua.
     */
    public function devoluciones()
    {
        return $this->hasMany(
            MantenimientoDevolucion::class,
            'id_mantenimiento',
            'id_mantenimiento'
        )->orderByDesc('fecha');
    }

    public function estaPorVencer(): bool
    {
        if (!$this->next_due_at) return false;

        return Carbon::parse($this->next_due_at)->isBetween(now(), now()->addDays(7));
    }
}
