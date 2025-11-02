<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    protected $table = 'beneficiario';
    protected $fillable = ['id', 'contrato_id', 'persona_id'];
}
