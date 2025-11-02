<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    protected $table = 'producto';
    protected $fillable = ['id', 'nombre', 'precio_unitario'];
    // es_serializado
}
