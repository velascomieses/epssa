<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    //
    protected $table = 'pago';

    protected $fillable = [
        'fecha_emision',
        'contrato_id',
        'oficina_id',
        'recibo',
        'tipo_comprobante_id',
        'serie_numero',
        'producto_id',
        'importe',
    ];

    public function contrato() {
        return $this->belongsTo(Contrato::class, 'contrato_id', 'id');
    }

    public function oficina() {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'id');
    }

}
