<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaInventario extends Model
{
    use HasFactory;

    protected $table      = 'area_inventarios';
    protected $primaryKey = 'id_areainventario';
    protected $fillable   = ['nombre', 'creador'];
    public    $incrementing = true;
    protected $keyType      = 'int';

    /* Relaciones */
    public function creadorUsuario()
    {
        return $this->belongsTo(User::class, 'creador');
    }


    public function subareas()
    {
        return $this->hasMany(
            SubareaInventario::class,
            'areapadre',
            'id_areainventario'
        );
    }
}
