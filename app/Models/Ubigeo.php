<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Ubigeo extends Model
{
    protected $table = 'ubigeo';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre'
    ];

    // Scope para distritos
    public function scopeDistritos(Builder $query): void
    {
        $query->where('id', 'not like', '%00');
    }

    // Obtener nombre completo del distrito
    public function getFullNameAttribute(): string
    {
        $departamento = substr($this->id, 0, 2) . '0000';
        $provincia = substr($this->id, 0, 4) . '00';

        $nombreDepartamento = static::where('id', $departamento)->value('nombre');
        $nombreProvincia = static::where('id', $provincia)->value('nombre');

        return "{$nombreDepartamento} - {$nombreProvincia} - {$this->nombre}";
    }
}
