<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cronograma extends Model
{
    //
    protected $table = 'cronograma';

    // primary key contrato_id, cuota
    protected $fillable = [
        'contrato_id',
        'cuota',
        'fecha_inicio',
        'fecha_vencimiento',
        'saldo',
        'capital',
        'interes',
        'importe',
        'estado',
    ];

    public function contrato()
    {
        return $this->belongsTo(contrato::class, 'contrato_id', 'id');
    }
}
