<?php

namespace App\Filament\Exports;

use App\Models\ContratoPersona;
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
            ExportColumn::make('contrato.id')->label('ID'),
            ExportColumn::make('contrato.fecha_atencion')->label('Fecha de atención')
                ->state(fn ($record): ?string => $record->contrato?->fecha_atencion),
            ExportColumn::make('contrato.tipoContrato.nombre')->label('Tipo'),
            ExportColumn::make('persona.nombre')->label('Contrante')
                ->state(fn ($record): ?string => $record->persona?->full_name),
            ExportColumn::make('contrato.beneficiarios')->label('Beneficiarios')
                ->state(fn ($record): ?string => $record->contrato?->beneficiarios?->map(fn ($b) => $b->persona?->full_name)->filter()->implode(', ')),
            ExportColumn::make('persona.telefono')->label('Teléfono'),
            ExportColumn::make('contrato.total')->label('Total'),
            ExportColumn::make('contrato.inicial')->label('Inicial'),
            ExportColumn::make('contrato.descuento')->label('Descuento'),
            ExportColumn::make('contrato.convenios')->label('Convenios')
                ->state(fn ($record): ?string => $record->contrato?->convenios?->map(fn ($c) => $c->persona?->full_name)->filter()->implode(', ')),
            ExportColumn::make('contrato.personal.nombre')->label('Interviniente')
                ->state(fn ($record): ?string => $record->contrato?->personal?->full_name),
            ExportColumn::make('contrato.numero_serie')->label('Código de ataúd'),
            ExportColumn::make('contrato.lugar_fallecimiento')->label('Lugar de fallecimiento'),
            ExportColumn::make('contrato.ubigeo.nombre')->label('Lugar de sepultura'),
            ExportColumn::make('contrato.cementerio.nombre')->label('Cementerio'),
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
