<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedioPago extends Model
{
    protected $table = 'medio_pago';

    protected $fillable = [
        'id',
        'nombre'
    ];
}
