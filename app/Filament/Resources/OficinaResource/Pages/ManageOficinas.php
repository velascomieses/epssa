<?php

namespace App\Filament\Resources\OficinaResource\Pages;

use App\Filament\Resources\OficinaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOficinas extends ManageRecords
{
    protected static string $resource = OficinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
