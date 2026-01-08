<?php

namespace App\Filament\Resources\CementerioResource\Pages;

use App\Filament\Resources\CementerioResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCementerios extends ManageRecords
{
    protected static string $resource = CementerioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
