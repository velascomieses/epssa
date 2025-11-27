<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use App\Models\Contrato;
use App\Services\CronogramaService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
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
            Actions\ActionGroup::make([
                Actions\EditAction::make(),
                Actions\Action::make('generarCronograma')
                    ->label('Generar cronograma')
                    ->action(function () {
                        try {
                            app(CronogramaService::class)->calcular($this->record->id);
                            Notification::make()
                                ->title('Cronograma generado con éxito.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al generar el cronograma')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->icon('heroicon-o-calendar')
                    ->requiresConfirmation()
                    ->visible(fn(Contrato $record): bool => $record->estado_id == null),
                Actions\Action::make('eliminarCronograma')
                    ->label('Eliminar cronograma')
                    ->action(function () {
                        try {
                            app(CronogramaService::class)->eliminar($this->record->id);

                            Notification::make()
                                ->title('Cronograma eliminado con éxito.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al eliminar el cronograma')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->visible(fn(Contrato $record): bool => $record->estado_id == 1),
            ])
                ->label('Opciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button(),
        ];
    }
    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return Contrato::findOrFail($key);
    }

    public function getTitle(): string
    {
        return $this->record->id;
    }
}
