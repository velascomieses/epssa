<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    //
    protected $table = 'pago';

    protected $fillable = [
        'fecha_emision',
        'fecha_calculo',
        'moneda_id',
        'contrato_id',
        'oficina_id',
        'recibo',
        'tipo_comprobante_id',
        'serie_numero',
        'producto_id',
        'importe',
        'estado',
        'referencia',
        'tipo_ingreso',
        'user_audit_id'
    ];

    public function contrato() {
        return $this->belongsTo(Contrato::class, 'contrato_id', 'id');
    }

    public function oficina() {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'id');
    }

    public function medioPago()
    {
        return $this->belongsTo(MedioPago::class, 'medio_pago_id', 'id');
    }
}
