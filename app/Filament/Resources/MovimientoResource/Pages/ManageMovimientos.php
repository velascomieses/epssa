<?php

namespace App\Filament\Resources\MovimientoResource\Pages;

use App\Filament\Resources\MovimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Movimiento;
use App\Models\ProductoItem;

class ManageMovimientos extends ManageRecords
{
    protected static string $resource = MovimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = Auth::id();
                    return $data;
                })
                ->after(function (Movimiento $record) {
                    DB::transaction(function () use ($record) {
                        foreach ($record->items as $movimientoItem) {
                            // Solo crear ProductoItem si no existe
                            if (!$movimientoItem->producto_item_id) {
                                $numeroSerie = $this->generarNumeroSerieUnico($movimientoItem->producto_id);

                                $productoItem = ProductoItem::create([
                                    'producto_id' => $movimientoItem->producto_id,
                                    'numero_serie' => $numeroSerie,
                                    'almacen_id' => $record->almacen_destino_id ?? $record->almacen_origen_id,
                                    'estado' => 'DISPONIBLE',
                                ]);

                                // Actualizar el MovimientoItem con el producto_item_id
                                $movimientoItem->update([
                                    'producto_item_id' => $productoItem->id,
                                ]);
                            }
                        }
                    });
                }),
        ];
    }

    private function generarNumeroSerieUnico(string $productoId): string
    {
        do {
            $numeroSerie = strtoupper(substr($productoId, 0, 4)) . '-' .
                date('Ymd') . '-' .
                strtoupper(substr(uniqid(), -6));

            $existe = ProductoItem::where('numero_serie', $numeroSerie)->exists();
        } while ($existe);

        return $numeroSerie;
    }
}
