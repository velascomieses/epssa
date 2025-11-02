<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    //
    protected $table = 'pago';

    public function contrato() {
        return $this->belongsTo(Contrato::class, 'contrato_id', 'id');
    }

}
