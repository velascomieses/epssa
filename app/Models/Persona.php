<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    //
    protected $table = 'persona';

    protected $fillable = [
        'nombre',
        'primer_apellido',
        'segundo_apellido',
        'numero_documento',
        'sexo',
        'fecha_nacimiento',
        'es_empresa',
        'es_convenio',
        'es_proveedor',
        'direccion',
        'telefono',
        'correo_electronico',
        'estado_civil_id',
        'tipo_documento_identidad_id',
        'ubigeo_id',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} {$this->primer_apellido} {$this->segundo_apellido}";
    }


}
