<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MovimientoItem extends Model
{
    use HasUuids;

    protected $table = 'movimiento_item';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'movimiento_id',
        'producto_id',
        'cantidad',
        'producto_item_id',
    ];

    public function movimiento()
    {
        return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    public function productoItem()
    {
        return $this->belongsTo(ProductoItem::class, 'producto_item_id', 'id');
    }
}
