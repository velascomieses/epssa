<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ContratoNota extends Model
{
    //
    use HasUuids;

    protected $table = 'contrato_nota';
    protected $fillable = [
        'id',
        'contrato_id',
        'nota',
        'user_audit_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_audit_id');
    }
}
