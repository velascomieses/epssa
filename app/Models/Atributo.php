<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    protected $table = 'atributo';
    protected $fillable = ['nombre', 'tipo', 'es_visible'];
}
