<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cementerio extends Model
{
    protected $table = 'cementerio';

    protected $fillable = [
        'nombre',
        'ubigeo_id'
    ];

    public $timestamps = false;

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }
    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} - {$this->ubigeo->nombre}";
    }
}
