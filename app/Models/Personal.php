<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personal extends Model
{
    //
    protected $table = 'personal';

    protected $fillable = ['tipo_documento_identidad_id', 'numero_documento', 'nombre', 'primer_apellido', 'segundo_apellido', 'estado'];

    public $timestamps = false;

    public function tipoDocumentoIdentidad(): BelongsTo
    {
        return $this->belongsTo(TipoDocumentoIdentidad::class, 'tipo_documento_identidad_id', 'id');
    }
    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} {$this->primer_apellido} {$this->segundo_apellido}";
    }
}
