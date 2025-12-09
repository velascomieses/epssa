<?php

namespace App\Filament\Resources\ProductoItemResource\Pages;

use App\Filament\Resources\ProductoItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductoItems extends ManageRecords
{
    protected static string $resource = ProductoItemResource::class;

    protected static ?string $title = 'Ítems de inventario';
    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
