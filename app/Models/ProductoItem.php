<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductoItem extends Model
{
    use HasUuids;

    protected $table = 'producto_item';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'proveedor_id',
        'producto_id',
        'numero_serie',
        'almacen_id',
        'estado', // DISPONIBLE, VENDIDO, RESERVADO, EN_MANTENIMIENTO, DAÑADO
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Persona::class, 'proveedor_id', 'id');
    }
}
