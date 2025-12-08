<?php

namespace App\Filament\Resources\AlmacenResource\Pages;

use App\Filament\Resources\AlmacenResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAlmacens extends ManageRecords
{
    protected static string $resource = AlmacenResource::class;

    protected static ?string $title = 'Almacenes';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
