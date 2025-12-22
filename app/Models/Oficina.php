<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oficina extends Model
{
    //
    protected $table = 'oficina';
    protected $fillable = ['nombre'];
    public  $timestamps = false;
}
