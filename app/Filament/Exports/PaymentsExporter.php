<?php

namespace App\Filament\Exports;

use App\Models\Pago;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;

class PaymentsExporter extends Exporter
{
    protected static ?string $model = Pago::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('recibo')
                ->label('N° Recibo'),
            ExportColumn::make('oficina')
                ->label('Oficina')
                ->formatStateUsing(fn (Pago $record): ?string => $record->oficina?->nombre),
            ExportColumn::make('fecha_emision')
                ->label('Fecha de pago')
                ->state(fn (Pago $record): ?string => Carbon::parse($record->fecha_emision)->format('d/m/Y')),
            ExportColumn::make('contrato.rolTitular.id')
                ->label('Titular')
                ->formatStateUsing(fn (Pago $record): ?string => $record->contrato?->rolTitular?->full_name),
            ExportColumn::make('medioPago.id')
                ->label('Medio de pago')
                ->formatStateUsing(fn (Pago $record): ?string => $record->medioPago?->nombre),
            ExportColumn::make('user.id')
                ->label('User')
                ->formatStateUsing(fn (Pago $record): ?string => $record->user?->name),
            ExportColumn::make('importe')
                ->label('Importe'),
        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de pagos se ha completado y ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' fallaron al exportar.';
        }

        return $body;
    }
}
