<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    protected $table = 'producto';
    protected $fillable = ['id', 'nombre', 'precio_unitario', 'es_serializado', 'user_audit_id'];
    // es_serializado

    public function productoAtributos()
    {
        return $this->hasMany(ProductoAtributo::class, 'producto_id');
    }

    public function atributos()
    {
        return $this->belongsToMany(Atributo::class, 'producto_atributo', 'producto_id', 'atributo_id')
            ->withPivot('valor');
    }
}
