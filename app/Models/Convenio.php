<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Convenio extends Model
{
    use HasUuids;

    protected $table = 'convenio';

    public $timestamps = false;

    protected $fillable = [
        'contrato_id',
        'persona_id',
        'fecha_tramite',
        'numero_expediente'
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
}
