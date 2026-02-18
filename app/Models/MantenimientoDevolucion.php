<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoDevolucion extends Model
{
    // Especifica la tabla si su nombre no sigue la convención plural de Eloquent
    protected $table = 'mantenimiento_devoluciones';

    // Si la tabla no tiene timestamps created_at/updated_at
    public $timestamps = false;

    // Llave primaria si no es "id"
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_mantenimiento',
        'equipo',
        'fecha',
        'devolucion',
    ];

    /**
     * Relación a Mantenimiento.
     */
    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(
            Mantenimiento::class,
            'id_mantenimiento',
            'id_mantenimiento'
        );
    }
}
