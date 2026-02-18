<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Relevamiento extends Model
{
    use HasFactory;

    /* ─────────────────────────  Config básicos  ───────────────────── */
    protected $table      = 'relevamiento';      // nombre de la tabla
    protected $primaryKey = 'id_relevamiento';
    public    $timestamps = false;                // la tabla no tiene created_at/updated_at

    /* Campos que se pueden asignar en masa (create / update) */
    protected $fillable = [
        'equipo',
        'titulorelevamiento',
        'detallerelevamiento',
        'foto1',
        'archivar',
        'fechacarga',
    ];

    /* Cast: que ‘fechacarga’ sea instancia de Carbon */
    protected $casts = [
        'fechacarga' => 'date',
    ];

    /* ─────────────────────────  Accessors  ────────────────────────── */

    /**
     * url pública de la foto (devuelve null si no hay imagen)
     */
    public function getFoto1UrlAttribute(): ?string
    {
        return $this->foto1
            ? Storage::disk('public')->url('relevamientos/'.$this->foto1)
            : null;
    }

    /* Al serializar a JSON/array se agrega foto1_url automáticamente  */
    protected $appends = ['foto1_url'];

    /* ─────────────────────────  Scopes útiles  ────────────────────── */

    /**
     * Scope para filtrar por rango de fechas (inclusive)
     *
     * Relevamiento::between($from,$to)->get();
     */
    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('fechacarga', [$from, $to]);
    }
}
