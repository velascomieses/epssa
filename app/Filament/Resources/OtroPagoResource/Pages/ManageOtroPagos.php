<?php

namespace App\Filament\Resources\OtroPagoResource\Pages;

use App\Filament\Resources\OtroPagoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOtroPagos extends ManageRecords
{
    protected static string $resource = OtroPagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
