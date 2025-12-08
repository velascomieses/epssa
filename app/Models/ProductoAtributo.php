<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ProductoAtributo extends Model
{
    use HasUuids;
    protected $table = 'producto_atributo';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['producto_id', 'atributo_id', 'valor'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'atributo_id', 'id');
    }
}
