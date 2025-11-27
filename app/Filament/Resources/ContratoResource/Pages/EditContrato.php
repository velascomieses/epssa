<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use App\Models\Contrato;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContrato extends EditRecord
{
    protected static string $resource = ContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return Contrato::findOrFail($key);
    }

    public function  getTitle() : string
    {
        return 'Editar contrato';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_audit_id'] = auth()->user()->id;
        return $data;
    }
}
