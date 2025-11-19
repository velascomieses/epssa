<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratoPersona extends Model
{
    protected $table = 'contrato_persona';
    protected $fillable = ['id', 'contrato_id', 'persona_id', 'rol_id'];
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
