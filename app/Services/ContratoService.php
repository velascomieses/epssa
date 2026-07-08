<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContratoService
{
    public function anular( int $id): void
    {
        try {
            DB::beginTransaction();
            /* Actualizar estado contrato */
            DB::table('contrato')->where('id', '=', $id )->update([
                'estado_id' => 3, 'user_audit_id' => auth()->user()->id, 'updated_at' => Carbon::now()
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }
}
