<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    //
    protected $table = 'contrato';

    protected $fillable = [
        'id',
        'tipo_contrato_id',
        'fecha_contrato',
        'numero_contrato',
        'categoria_id',
        'numero_serie',
        'inicial',
        'descuento',
        'total',
        'numero_cuotas',
        'dia',
        'fecha_vencimiento',
        'tea',
        'oficina_id',
        'personal_id',
        'fecha_atencion',
        'lugar_fallecimiento',
        'direccion_velatorio',
        'ubigeo_id'
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
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }
    public function beneficiarios()
    {
        return $this->contratoPersonas()->where('rol_id', 3);
    }
    public function rolTitular()
    {
        return $this->hasOneThrough(
            Persona::class,
            ContratoPersona::class,
            'contrato_id', // Clave foránea en contrato_persona
            'id',   // Clave primaria en persona
            'id', // Clave local en contrato
            'persona_id'    // Clave foránea en contrato_persona que conecta con persona
        )->where('contrato_persona.rol_id', 1);
    }
    public function cronograma()
    {
        return $this->hasMany(Cronograma::class, 'contrato_id', 'id');
    }

    public function amortizacion()
    {
        return $this->hasMany(Amortizacion::class, 'contrato_id', 'id');
    }

    public function notas()
    {
        return $this->hasMany(ContratoNota::class, 'contrato_id', 'id');
    }

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id', 'id');
    }
}
