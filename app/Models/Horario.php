<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'dia',
        'horarioentrada',
        'horariosalida',
        'ausente',
        'equipo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
        public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
