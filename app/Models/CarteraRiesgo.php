<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteraRiesgo extends Model
{
    protected $table = 'cartera_riesgo';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'solicitud_id',
        'titular_id',
        'consejero_id',
        'dias_atraso',
        'monto_capital',
        'monto_pendiente',
        'num_cuotas_vencidas',
        'ultima_fecha_pago',
        'categoria_riesgo',
        'total_contrato',
        'fecha_evaluacion',
        'tipo_solicitud'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function titular()
    {
        return $this->belongsTo(Persona::class, 'titular_id', 'id');
    }
}
