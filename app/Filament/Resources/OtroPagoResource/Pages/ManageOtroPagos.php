<?php

namespace App\Filament\Resources\OtroPagoResource\Pages;

use App\Filament\Resources\OtroPagoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOtroPagos extends ManageRecords
{
    protected static string $resource = OtroPagoResource::class;

    public static ?string $title = 'Otros pagos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->mutateFormDataUsing(function(array $data): array {
                $data['user_audit_id'] = auth()->user()->id;
                $data['fecha_calculo'] = $data['fecha_emision'];
                $data['moneda_id'] = 1; // moneda local
                $data['estado'] = 0;
                $data['tipo_ingreso'] = 2; // otro pago
                return $data;
            })
        ];
    }
}
