<?php

namespace App\Filament\Exports;

use App\Models\ProductoItem;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InventoryExporter extends Exporter
{
    protected static ?string $model = ProductoItem::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('producto_id')->label('Producto')
                ->formatStateUsing(function ($record) {
                    $nombre = $record->producto?->nombre;
                    $badges = $record->producto?->productoAtributos
                        ->map(fn ($pa) =>
                            $pa->atributo?->nombre . ': ' . $pa->valor
                        )
                        ->implode(' ') ?? '';

                    return $nombre . ($badges ? " {$badges}" : '');
                }),
            ExportColumn::make('proveedor_id')->label('Proveedor')
                ->state(fn ($record) => $record->proveedor?->full_name),
            ExportColumn::make('almacen_id')->label('Almacén')
                ->state(fn ($record) => $record->almacen?->nombre),
            ExportColumn::make('numero_serie')->label('Número de serie de ataúd'),
            ExportColumn::make('estado')->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your inventory export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
