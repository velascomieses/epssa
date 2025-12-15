<?php

namespace App\Filament\Resources\MovimientoResource\Pages;

use App\Filament\Resources\MovimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Movimiento;
use App\Models\ProductoItem;
use App\Models\Persona;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use App\Models\Almacen;
use App\Models\Producto;
class ManageMovimientos extends ManageRecords
{
    protected static string $resource = MovimientoResource::class;

//    protected function getHeaderActions(): array
//    {
//        return [
//            Actions\CreateAction::make()
//                ->mutateFormDataUsing(function (array $data): array {
//                    $data['user_id'] = Auth::id();
//                    return $data;
//                })
//                ->after(function (Movimiento $record) {
//                    DB::transaction(function () use ($record) {
//                        foreach ($record->items as $movimientoItem) {
//                            // Solo crear ProductoItem si no existe
//                            if (!$movimientoItem->producto_item_id) {
//                                $numeroSerie = $this->generarNumeroSerieUnico($movimientoItem->producto_id);
//
//                                $productoItem = ProductoItem::create([
//                                    'producto_id' => $movimientoItem->producto_id,
//                                    'numero_serie' => $numeroSerie,
//                                    'almacen_id' => $record->almacen_destino_id ?? $record->almacen_origen_id,
//                                    'estado' => 'DISPONIBLE',
//                                ]);
//
//                                // Actualizar el MovimientoItem con el producto_item_id
//                                $movimientoItem->update([
//                                    'producto_item_id' => $productoItem->id,
//                                ]);
//                            }
//                        }
//                    });
//                }),
//        ];
//    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('entrada')
                ->label('Entrada')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    DatePicker::make('fecha_movimiento')
                        ->required()
                        ->default(now()),
                    Select::make('proveedor_id')
                        ->relationship('proveedor', 'proveedor_id')
                        ->label('Proveedor')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array =>
                            Persona::where('es_proveedor', true)
                            ->where(function ($query) use ($search) {
                                $query->whereRaw("CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) LIKE ?", ["%{$search}%"])
                                    ->orWhere('numero_documento', 'like', "%{$search}%");
                            })
                            ->get()
                            ->mapWithKeys(fn ($persona) => [$persona->id => $persona->full_name])
                            ->toArray()
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name}"),
                    Select::make('almacen_destino_id')
                        ->label('Almacén Destino')
                        ->options(Almacen::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable(),
                    TableRepeater::make('items')
                        ->headers([
                            Header::make('producto_id')->label('Producto')->width('200px'),
                        ])
                        ->schema([
                            Select::make('producto_id')
                                ->label('Producto')
                                ->searchable()
                                ->required()
                                ->columnSpan(2)
                                ->allowHtml()
                                ->getSearchResultsUsing(fn (string $search): array =>
                                Producto::where('es_serializado', true)
                                    ->where('nombre', 'like', "%{$search}%")
                                    ->with('productoAtributos.atributo')
                                    ->get()
                                    ->mapWithKeys(function ($producto) {
                                        $badges = $producto->productoAtributos
                                            ->map(fn ($pa) =>
                                                "<span class='inline-flex items-center gap-x-1 rounded-md px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'>" .
                                                ($pa->atributo?->nombre ?? 'N/A') . ': ' . $pa->valor .
                                                "</span>"
                                            )
                                            ->implode(' ');

                                        $label = $producto->nombre . ($badges ? "<br><div class='mt-1'>{$badges}</div>" : '');
                                        return [$producto->id => $label];
                                    })
                                    ->toArray()
                                ),
                        ])
                        ->defaultItems(1)
                        ->addActionLabel('Agregar producto'),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $movimiento = Movimiento::create([
                            'tipo_movimiento' => 'ENTRADA',
                            'fecha_movimiento' => $data['fecha_movimiento'],
                            'proveedor_id' => $data['proveedor_id'],
                            'almacen_destino_id' => $data['almacen_destino_id'],
                            'user_id' => Auth::id(),
                        ]);

                        foreach ($data['items'] as $item) {
                            $numeroSerie = $this->generateUniqueSerialNumber($item['producto_id']);

                            $productoItem = ProductoItem::create([
                                'producto_id' => $item['producto_id'],
                                'numero_serie' => $numeroSerie,
                                'almacen_id' => $data['almacen_destino_id'],
                                'estado' => 'DISPONIBLE',
                            ]);

                            $movimiento->items()->create([
                                'producto_id' => $item['producto_id'],
                                'producto_item_id' => $productoItem->id,
                            ]);
                        }
                    });
                }),

            Actions\Action::make('transferencia')
                ->label('Transferencia')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    DatePicker::make('fecha_movimiento')
                        ->required()
                        ->default(now()),
                    Select::make('almacen_origen_id')
                        ->label('Almacén Origen')
                        ->options(Almacen::all()->pluck('nombre', 'id'))
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('almacen_destino_id', null)),
                    Select::make('almacen_destino_id')
                        ->label('Almacén Destino')
                        ->options(fn ($get) =>
                            Almacen::where('id', '!=', $get('almacen_origen_id'))
                                ->pluck('nombre', 'id')
                        )
                        ->required()
                        ->searchable(),
                    TableRepeater::make('items')
                        ->headers([
                            Header::make('producto_item_id')->label('Producto')->width('300px'),
                        ])
                        ->schema([
                            Select::make('producto_item_id')
                                ->label('Producto')
                                ->required()
                                ->searchable()
                                ->allowHtml()
                                ->options(function ($get) {
                                    $almacenOrigenId = $get('../../almacen_origen_id');
                                    if (!$almacenOrigenId) return [];

                                    return ProductoItem::with('producto.productoAtributos.atributo')
                                        ->where('almacen_id', $almacenOrigenId)
                                        ->where('estado', 'DISPONIBLE')
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            $badges = $item->producto->productoAtributos
                                                ->map(fn ($pa) =>
                                                    "<span class='inline-flex items-center gap-x-1 rounded-md px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700'>" .
                                                    ($pa->atributo?->nombre ?? 'N/A') . ': ' . $pa->valor .
                                                    "</span>"
                                                )
                                                ->implode(' ');

                                            $label = $item->producto->nombre .
                                                    " <span class='text-gray-500'>[{$item->numero_serie}]</span>" .
                                                    ($badges ? "<br><div class='mt-1'>{$badges}</div>" : '');
                                            return [$item->id => $label];
                                        })
                                        ->toArray();
                                })
                                ->columnSpanFull(),
                        ])
                        ->defaultItems(1)
                        ->addActionLabel('Agregar producto'),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $movimiento = Movimiento::create([
                            'tipo_movimiento' => 'TRASFERENCIA',
                            'fecha_movimiento' => $data['fecha_movimiento'],
                            'almacen_origen_id' => $data['almacen_origen_id'],
                            'almacen_destino_id' => $data['almacen_destino_id'],
                            'user_id' => Auth::id(),
                        ]);

                        foreach ($data['items'] as $item) {
                            $productoItem = ProductoItem::findOrFail($item['producto_item_id']);

                            // Actualizar ubicación del producto
                            $productoItem->update([
                                'almacen_id' => $data['almacen_destino_id'],
                            ]);

                            // Registrar movimiento
                            $movimiento->items()->create([
                                'producto_id' => $productoItem->producto_id,
                                'producto_item_id' => $productoItem->id,
                            ]);
                        }
                    });
                }),
//            Actions\Action::make('salida')
//                ->label('Nueva Salida')
//                ->icon('heroicon-o-arrow-up-tray')
//                ->color('danger')
//                ->form([
//                    DatePicker::make('fecha_movimiento')
//                        ->required()
//                        ->default(now()),
//                    Select::make('almacen_origen_id')
//                        ->label('Almacén Origen')
//                        ->options(Almacen::all()->pluck('nombre', 'id'))
//                        ->required()
//                        ->searchable()
//                        ->live(),
//                    TableRepeater::make('items')
//                        ->headers([
//                            Header::make('producto_item_id')->label('Producto')->width('300px'),
//                        ])
//                        ->schema([
//                            Select::make('producto_item_id')
//                                ->label('Producto')
//                                ->required()
//                                ->searchable()
//                                ->allowHtml()
//                                ->options(function ($get) {
//                                    $almacenOrigenId = $get('../../almacen_origen_id');
//                                    if (!$almacenOrigenId) return [];
//
//                                    return ProductoItem::with('producto')
//                                        ->where('almacen_id', $almacenOrigenId)
//                                        ->where('estado', 'DISPONIBLE')
//                                        ->get()
//                                        ->mapWithKeys(fn ($item) => [
//                                            $item->id => $item->producto->nombre .
//                                                        " <span class='text-gray-500'>[{$item->numero_serie}]</span>"
//                                        ])
//                                        ->toArray();
//                                })
//                                ->columnSpanFull(),
//                        ])
//                        ->defaultItems(1)
//                        ->addActionLabel('Agregar producto'),
//                ])
//                ->action(function (array $data) {
//                    DB::transaction(function () use ($data) {
//                        $movimiento = Movimiento::create([
//                            'tipo_movimiento' => 'SALIDA',
//                            'fecha_movimiento' => $data['fecha_movimiento'],
//                            'almacen_origen_id' => $data['almacen_origen_id'],
//                            'user_id' => Auth::id(),
//                        ]);
//
//                        foreach ($data['items'] as $item) {
//                            $productoItem = ProductoItem::findOrFail($item['producto_item_id']);
//
//                            // Marcar como no disponible
//                            $productoItem->update([
//                                'estado' => 'NO_DISPONIBLE',
//                            ]);
//
//                            $movimiento->items()->create([
//                                'producto_id' => $productoItem->producto_id,
//                                'producto_item_id' => $productoItem->id,
//                            ]);
//                        }
//                    });
//                }),
        ];
    }
    private function generateUniqueSerialNumber(string $productoId): string
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
