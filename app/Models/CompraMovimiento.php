<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraMovimiento extends Model
{
    protected $table = 'compras_movimientos';

    protected $fillable = [
        'compra_id', 'user_id', 'accion', 'cambios', 'comentario',
    ];

    // Si en DB guardamos JSON como texto (LONGTEXT), lo decodificamos al vuelo
    protected function serializeDate(\DateTimeInterface $date) { return $date->format('Y-m-d H:i:s'); }

    public function getCambiosAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        }
        return $value;
    }

    public function compra()  { return $this->belongsTo(\App\Models\Compra::class, 'compra_id'); }
    public function user()    { return $this->belongsTo(\App\Models\User::class, 'user_id'); }
}
