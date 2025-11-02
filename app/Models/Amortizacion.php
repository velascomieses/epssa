<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amortizacion extends Model
{
    //
    protected $table = 'amortizacion';
    // primary key pago_id, cuota
    protected $fillable = [
        'pago_id',
        'cuota',
        'contrato_id',
        'capital',
        'interes',
        'mora',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id', 'id');
    }

}
