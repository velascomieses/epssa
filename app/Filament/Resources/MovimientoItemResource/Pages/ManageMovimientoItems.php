<?php

namespace App\Filament\Resources\MovimientoItemResource\Pages;

use App\Filament\Resources\MovimientoItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMovimientoItems extends ManageRecords
{
    protected static string $resource = MovimientoItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
