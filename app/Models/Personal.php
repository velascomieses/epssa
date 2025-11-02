<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    //
    protected $table = 'personal';

    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} {$this->primer_apellido} {$this->segundo_apellido}";
    }
}
