<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    protected $table = 'convenio';

    public $timestamps = false;

    protected $fillable = [
        'contrato_id',
        'persona_convenio_id',
    ];

}
