<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use App\Filament\Resources\PersonalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePersonals extends ManageRecords
{
    protected static string $resource = PersonalResource::class;

    protected static ?string $title = 'Personal';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
