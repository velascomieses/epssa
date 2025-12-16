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
    protected static ?string $model = ItemPersona::class;

    public static function getColumns(): array
    {
        return [
            //
            ExportColumn::make('solicitud.solicitud_id')->label('ID'),
            ExportColumn::make('solicitud.modificacion')->label('Fecha')
            ->state(fn (ItemPersona $record): ?string => Carbon::parse($record->solicitud?->modificacion)->format('d/m/Y')),
            ExportColumn::make('persona.nombres')->label('Nombres y apellidos')
            ->formatStateUsing(fn (ItemPersona $record): ?string => $record->persona?->full_name),
            ExportColumn::make('solicitud.tipo_solicitud')->label('Tipo')
            ->formatStateUsing(fn (ItemPersona $record): ?string => $record->solicitud?->tipo_solicitud === 'F' ? 'N. Futura' : 'N. Inmediata'),
            ExportColumn::make('solicitud.estado')->label('Estado')
            ->formatStateUsing(fn (ItemPersona $record): ?string => $record->solicitud?->estado->descripcion),
            ExportColumn::make('solicitud.itemSepulturas')->label('Servicio sepultura')
            ->formatStateUsing(fn (ItemPersona $record): ?string => $record->solicitud?->itemSepulturas?->pluck('servicioSepultura.descripcion')->join(', ')),
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
