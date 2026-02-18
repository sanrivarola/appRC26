<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubareaInventario extends Model
{
    use HasFactory;

    protected $table      = 'inventario_subareas';
    protected $primaryKey = 'id_subareasinventario';
    public    $incrementing = true;
    protected $keyType      = 'int';

    protected $fillable = ['nombre', 'areapadre'];

    /* Relaciones */
    public function areaPadre()
    {
        return $this->belongsTo(AreaInventario::class,
                                'areapadre',          // FK en esta tabla
                                'id_areainventario'); // PK en la tabla padre
    }
}
