<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Relevamiento extends Model
{
    use HasFactory;

    /* ─────────────────────────  CONFIG  ───────────────────────── */

    protected $table = 'relevamiento';

    protected $primaryKey = 'id_relevamiento';

    public $timestamps = false;

    protected $keyType = 'int';

    public $incrementing = true;

    /* ─────────────────────────  MASS ASSIGNMENT  ───────────────── */

    protected $fillable = [
        'equipo',
        'titulorelevamiento',
        'detallerelevamiento',
        'foto1',
        'archivar',
        'fechacarga',
    ];

    /* ─────────────────────────  CASTS  ─────────────────────────── */

    protected $casts = [
        'fechacarga' => 'date',
    ];

    /* ─────────────────────────  APPENDS  ───────────────────────── */

    protected $appends = [
        'foto1_url',
    ];

    /* ─────────────────────────  ROUTE MODEL BINDING  ───────────── */

    public function getRouteKeyName()
    {
        return 'id_relevamiento';
    }

    /* ─────────────────────────  ACCESSORS  ─────────────────────── */

    /**
     * URL pública de la imagen
     */
    public function getFoto1UrlAttribute(): ?string
    {
        if (!$this->foto1) {
            return null;
        }

        return Storage::disk('public')
            ->url('relevamientos/' . $this->foto1);
    }

    /**
     * Saber si está archivado
     */
    public function getIsArchivedAttribute(): bool
    {
        return $this->archivar === 'si';
    }

    /* ─────────────────────────  SCOPES  ────────────────────────── */

    /**
     * Filtrar por rango de fechas
     */
    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween(
            'fechacarga',
            [$from, $to]
        );
    }

    /**
     * Sólo activos
     */
    public function scopeActive($query)
    {
        return $query->where('archivar', 'no');
    }

    /**
     * Sólo archivados
     */
    public function scopeArchived($query)
    {
        return $query->where('archivar', 'si');
    }
}