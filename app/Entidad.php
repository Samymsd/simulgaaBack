<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidad extends Model
{
    use SoftDeletes;
    protected $table = "entidades";
    protected $fillable = array('nombre');
    protected $dates = ['deleted_at'];

}
