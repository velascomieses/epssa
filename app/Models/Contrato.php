<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    //
    protected $table = 'contrato';

    protected $fillable = [
        'id', 'tipo_contrato_id', 'fecha_contrato', 'numero_contrato', 'titular_id',
    ];

    public function titular()
    {
        return $this->belongsTo(Persona::class, 'titular_id', 'id');
    }
    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id', 'id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'personal_id', 'id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'id');
    }

    public function productos()
    {
        return $this->hasMany(ContratoProducto::class, 'contrato_id', 'id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'contrato_id', 'id');
    }

    public function convenios()
    {
        return $this->hasMany(Convenio::class, 'contrato_id', 'id');
    }

    public function contratoPersonas()
    {
        return $this->hasMany(ContratoPersona::class, 'contrato_id', 'id');
    }

    public function cronograma()
    {
        return $this->hasMany(Cronograma::class, 'contrato_id', 'id');
    }

    public function amortizacion()
    {
        return $this->hasMany(Amortizacion::class, 'contrato_id', 'id');
    }

}
