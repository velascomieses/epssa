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
        'numero_documento'
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} {$this->primer_apellido} {$this->segundo_apellido}";
    }


}
