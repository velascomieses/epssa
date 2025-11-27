<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use App\Models\Contrato;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContrato extends CreateRecord
{
    protected static string $resource = ContratoResource::class;

    public function getModel(): string
    {
        return Contrato::class;
    }
    public function getTitle(): string
    {
        return 'Crear contrato';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_audit_id'] = auth()->user()->id;
        return $data;
    }
}
