<?php

namespace App\Filament\Exports;

use App\Models\Contracts;
use App\Models\ItemPersona;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;

class ContractsExporter extends Exporter
{
    protected static ?string $model = ContratoPersona::class;

    public static function getColumns(): array
    {
        return [
            //
            ExportColumn::make('contrato.id')->label('ID'),
            ExportColumn::make('contrato.fecha_contrato')->label('Fecha')
            ->state(fn ($record): ?string => Carbon::parse($record->contrato?->fecha_contrato)->format('d/m/Y')),
            ExportColumn::make('persona.nombre')->label('Nombres y apellidos')
            ->formatStateUsing(fn ($record): ?string => $record->persona?->full_name),
            ExportColumn::make('contrato.tipoContrato.nombre')->label('Tipo'),
            ExportColumn::make('contrato.estado.nombre')->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your contracts export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
