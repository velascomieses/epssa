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
            ExportColumn::make('pago_id')
                ->label('ID'),
            ExportColumn::make('num_recibo')
                ->label('N° Recibo'),
            ExportColumn::make('fecha')
                ->label('Fecha de pago')
                ->state(fn (Pago $record): ?string => Carbon::parse($record->fecha)->format('d/m/Y')),
            ExportColumn::make('persona.nombres')
                ->label('Titular')
                ->formatStateUsing(fn (Pago $record): ?string => $record->persona?->full_name),
            ExportColumn::make('personalCobranza.nombres')
                ->label('Personal de cobranza')
                ->formatStateUsing(fn (Pago $record): ?string => $record->personalCobranza?->full_name),
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
