<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ResidenciaHorario extends Model
{
    use HasFactory;

    protected $table = 'residencia_horarios';
    protected $primaryKey = 'id_residencia_horario';

    protected $fillable = [
        'user_id',
        'horario_ingreso',
        'horario_egreso',
        'dias_transcurridos',
        'horas_transcurridas',
        'observaciones',
    ];

    protected $casts = [
        'horario_ingreso' => 'datetime',
        'horario_egreso'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Calcula y devuelve los días entre ingreso y egreso.
     * Si no hay egreso aún, calcula desde ingreso hasta hoy.
     */
    public function calcularDias(): int
    {
        $desde = Carbon::parse($this->horario_ingreso);
        $hasta = $this->horario_egreso
            ? Carbon::parse($this->horario_egreso)
            : Carbon::now();

        return (int) $desde->diffInDays($hasta);
    }
}
