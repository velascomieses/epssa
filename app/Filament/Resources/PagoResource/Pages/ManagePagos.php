<?php

namespace App\Filament\Resources\PagoResource\Pages;

use App\Filament\Resources\PagoResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagePagos extends ManageRecords
{
    protected static string $resource = PagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(true)
                ->using(function (array $data): ?Model {
                    try {
                        if (!Auth::user()->hasRole('admin')) {
                            $data['fecha_calculo'] = $data['fecha_emision'];
                        }
                        DB::beginTransaction();
                        // ( contrato_id INT, fecha_emision DATE, fecha_calculo DATE, moneda_id INT, recibo VARCHAR(255), personal_id INT, importe DECIMAL(16,2), tipo_comprobante_id INT, operacion INT, oficina_id INT, tipo_ingreso INT, referencia TEXT, OUT pago_id INT
                        $result = DB::select('CALL `sp_payments`( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @id )', [
                            $data['contrato_id'],
                            $data['fecha_emision'], // fecha1
                            $data['fecha_calculo'],
                            1, // moneda_id
                            $data['recibo'], // num_recibo
                            $data['importe'],
                            $data['tipo_comprobante_id'], // comprobante_id 3 RECIBO
                            $data['oficina_id'], // oficina_id
                            1, // tipo_ingreso 1 pago 2 otro pago
                            $data['referencia'], // referencia
                            $data['medio_pago_id'], // medio_pago_id
                            Carbon::now(), // created_at
                            Auth::user()->id,
                        ]);
                        // commit transaction
                        DB::commit();
                        Notification::make()
                            ->title('Pago realizado con éxito.')
                            ->success()
                            ->send();
                        return null;
                    } catch (QueryException $exception) {
                        // rollback transaction
                        DB::rollBack();
                        Notification::make()
                            ->title('Error al guardar pago.')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                        throw $exception;
                    }
                })
            ,
        ];
    }
}
