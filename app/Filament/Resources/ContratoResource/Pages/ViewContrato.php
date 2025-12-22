<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use App\Models\Contrato;
use App\Models\ContratoNota;
use App\Models\ProductoItem;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewContrato extends ViewRecord
{
    protected static string $resource = ContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('verContrato')
                    ->label('Contrato')
                    ->url(fn ($record) => route('contratos.contrato', ['id' => $record->id]))
                    ->icon('heroicon-o-document-text')
                    ->openUrlInNewTab(),
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
                Actions\Action::make('verHistorialOtrosPagos')
                    ->label('Historial de Otros Pagos')
                    ->url(fn ($record) => route('contratos.rpt.historial.otros.pagos', ['id' => $record->id]))
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
                Actions\Action::make('registrarSalida')
                    ->label('Registrar Salida')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('danger')
                    ->visible(fn(Contrato $record): bool =>
                        !empty($record->numero_serie) &&
                        ProductoItem::where('numero_serie', $record->numero_serie)
                            ->where('estado', 'DISPONIBLE')
                            ->exists()
                    )
                    ->action(function (Contrato $record) {
                        $productoItem = ProductoItem::where('numero_serie', $record->numero_serie)
                            ->where('estado', 'DISPONIBLE')
                            ->first();

                        if (!$productoItem) {
                            Notification::make()
                                ->title('Error')
                                ->body('Número de serie no encontrado o producto no disponible.')
                                ->danger()
                                ->send();
                            return;
                        }

                        DB::transaction(function () use ($record, $productoItem) {
                            $movimiento = \App\Models\Movimiento::create([
                                'tipo_movimiento' => 'SALIDA',
                                'fecha_movimiento' => now(),
                                'almacen_origen_id' => $productoItem->almacen_id,
                                'user_id' => Auth::id(),
                            ]);

                            $productoItem->update(['estado' => 'VENDIDO']);

                            $movimiento->items()->create([
                                'producto_id' => $productoItem->producto_id,
                                'producto_item_id' => $productoItem->id,
                            ]);
                        });

                        Notification::make()
                            ->title('Salida registrada correctamente')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Registrar Salida de Inventario')
                    ->modalDescription('Se registrará la salida del producto y se marcará como VENDIDO.'),
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
                Actions\Action::make('notas')
                    ->label('Notas de seguimiento')
                    ->mountUsing(function (\Filament\Forms\Form $form, Contrato $record) {
                        $form->fill([
                            'contrato_id' => $record->id,
                            'notas_anteriores' => $record->notas->map(fn($nota) => [
                                'nota' => $nota->nota,
                                'created_at' => $nota->created_at,
                                'user_name' => $nota->user->name,
                            ])->toArray(),
                        ]);
                    })
                    ->form([
                        Hidden::make('contrato_id'),
                        TableRepeater::make('notas_anteriores')
                            ->hiddenLabel(true)
                            ->headers([
                                Header::make('nota')->label('Nota'),
                                Header::make('created_at')->label('Fecha de Creación'),
                                Header::make('user_audit.name')->label('Creado por'),
                            ])
                            ->label('Notas de seguimiento')
                            ->disabled()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('nota')
                                    ->label('Nota')
                                    ->rows(2)
                                    ->disabled(),
                                \Filament\Forms\Components\TextInput::make('created_at')
                                    ->label('Fecha de Creación')
                                    ->disabled()
                                    ->formatStateUsing(fn($state)=> Carbon::parse($state)->format('d/m/Y H:i')),
                                \Filament\Forms\Components\TextInput::make('user_name')
                                    ->label('Creado por')
                                    ->disabled(),
                            ])
                            ->columnSpan('full')
                            ->hidden(fn (\Filament\Forms\Get $get): bool => empty($get('notas_anteriores'))),
                        Textarea::make('nota')
                            ->hiddenLabel(true)
                            ->rows(3)
                            ->required()
                            ->maxLength(500)
                            ->placeholder('Escribe tus notas de seguiento aquí...'),
                    ])
                    ->action(function (array $data) {
                        $contratoNota = new ContratoNota();
                        $contratoNota->contrato_id = $data['contrato_id'];
                        $contratoNota->user_audit_id = auth()->user()->id;
                        $contratoNota->nota = $data['nota'];
                        $contratoNota->save();

                        Notification::make()
                            ->title('Nota agregada con éxito')
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-chat-bubble-left'),
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
