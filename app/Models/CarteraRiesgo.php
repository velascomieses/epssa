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
        'contrato_id',
        'titular_id',
        'personal_id',
        'dias_atraso',
        'monto_capital',
        'monto_pendiente',
        'num_cuotas_vencidas',
        'ultima_fecha_pago',
        'categoria_riesgo',
        'total_contrato',
        'fecha_evaluacion',
        'tipo_contrato_id'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function titular()
    {
        return $this->belongsTo(Persona::class, 'titular_id', 'id');
    }
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'personal_id', 'id');
    }
}
