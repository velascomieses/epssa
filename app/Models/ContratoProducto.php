<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratoProducto extends Model
{
    //
    protected $table = 'contrato_producto';
    protected $fillable = ['id', 'contrato_id', 'producto_id', 'cantidad', 'precio_unitario', 'importe'];
}
