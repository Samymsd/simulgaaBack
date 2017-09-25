<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReunion extends Model
{
    use SoftDeletes;
    protected $table = "user_reunion";
    protected $fillable = array('user_id', 'reunion_id','asistencia','tipo_participante');
    protected $dates = ['deleted_at'];
}
