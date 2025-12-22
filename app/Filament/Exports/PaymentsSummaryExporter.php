<?php

namespace App\Filament\Exports;

use App\Models\ResumenPago;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PaymentsSummaryExporter extends Exporter
{
    protected static ?string $model = ResumenPago::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('year')
                ->label('Año'),
            ExportColumn::make('month_name')
                ->label('Mes'),
            ExportColumn::make('producto.nombre')
                ->label('Concepto')
                ->formatStateUsing(fn (ResumenPago $record): ?string => $record->producto_id == 0 ? 'Pago por cuotas' : $record->producto?->nombre),
            ExportColumn::make('total')
                ->label('Total'),
        ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your payments summary export export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
