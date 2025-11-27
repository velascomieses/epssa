<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ContratoPersona extends Model
{
    use HasUuids;

    protected $table = 'contrato_persona';

    protected $fillable = ['id', 'rol_id', 'persona_id', 'contrato_id' ];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id', 'id');
    }
    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }
    // Relación con RolPersona
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }

    public function getRouteKeyName()
    {
        return 'contrato_id';
    }
}
