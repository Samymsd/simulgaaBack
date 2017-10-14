<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Reunion extends Model
{
    use SoftDeletes;
    protected $table = "reuniones";

    protected $fillable = [
        'estado',
        'asunto',
        'descripcion',
        'participacion_minima',
        'lugar',
        'hora_inicial','hora_final',
        'fecha',
        'prioridad',
        'hastaNegociacion',
        'hastaRepetir',
        'tipo'
    ];

    protected $dates = ['deleted_at'];


    /**
     * The roles that belong to the user.
     */

    /*
    public function usuarios()
    {
        return $this->belongsToMany('App\User');
    }
  */

    public function UserReunion(){
        return $this->hasMany('App\UserReunion', 'reunion_id', 'id');
    }
}
