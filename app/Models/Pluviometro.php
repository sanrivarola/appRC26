<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pluviometro extends Model
{
    protected $table      = 'pluviometro';
    protected $primaryKey = 'id_pluviometro';

    protected $fillable = [
        'fechacarga','medicion','observaciones',
        'equipo','creador',
    ];

    /* Relación con User (equipo = name)  */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'equipo', 'name');
    }
}
