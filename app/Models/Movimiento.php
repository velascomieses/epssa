<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasUuids;
    //
    protected $table = 'movimiento';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tipo_movimiento', // ENTRADA, SALIDA, TRASFERENCIA
        'fecha_movimiento',
        'proveedor_id',
        'almacen_origen_id',
        'almacen_destino_id',
        'user_id',
    ];

    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id', 'id');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Persona::class, 'proveedor_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(MovimientoItem::class, 'movimiento_id', 'id');
    }
}
