<?php

namespace App\Filament\Exports;

use App\Filament\Widgets\RiskPortfolio;
use App\Models\CarteraRiesgo;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;
class RiskPortfolioExporter extends Exporter
{
    protected static ?string $model = CarteraRiesgo::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('contrato_id')
                ->label('ID'),
            ExportColumn::make('fecha_contrato')
                ->label('Fecha de contrato'),
            ExportColumn::make('tipo_contrato_id')
                ->label('Tipo de contrato'),
            ExportColumn::make('titular.id')
                ->label('Titular')
                ->formatStateUsing(fn (CarteraRiesgo $record) => $record->titular?->full_name),
            ExportColumn::make('categoria_riesgo')
                ->label('Categoría de riesgo'),
            ExportColumn::make('dias_atraso')
                ->label('Dias de atraso'),
            ExportColumn::make('personal.id')
                ->label('Interviniente')
                ->formatStateUsing(fn (CarteraRiesgo $record) => $record->personal?->full_name),
            ExportColumn::make('monto_capital')
                ->label('Total capital'),
            ExportColumn::make('monto_pendiente')
                ->label('Total capital'),
            ExportColumn::make('num_cuotas_vencidas')
                ->label('Cuotas vencidas'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your risk portfolio export has completed and ' . number_format($export->successful_rows) . ' ' . Str::plural('row',$export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row',$failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
