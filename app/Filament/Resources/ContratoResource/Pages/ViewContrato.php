<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;

class ViewContrato extends ViewRecord
{
    protected static string $resource = ContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('verCronograma')
                    ->label('Cronograma')
                    ->url(fn ($record) => route('contratos.rpt.cronograma', ['id' => $record->id]))
                    ->icon('heroicon-o-calendar')
                    ->openUrlInNewTab(),
                Actions\Action::make('verHistorialPago')
                    ->label('Historial de Pagos')
                    ->url(fn ($record) => route('contratos.rpt.historial.pago', ['id' => $record->id]))
                    ->icon('heroicon-o-document-text')
                    ->openUrlInNewTab(),
            ])
            ->label('Documentos')
            ->icon('heroicon-m-document-text')
            ->size(ActionSize::Small)
            ->color('info')
            ->button(),
            Actions\EditAction::make(),
        ];
    }
}
