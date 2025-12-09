<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovimientoResource\Pages;
use App\Filament\Resources\MovimientoResource\RelationManagers;
use App\Models\Almacen;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\Producto;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Awcodes\TableRepeater\Components\TableRepeater;

class MovimientoResource extends Resource
{
    protected static ?string $model = Movimiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tipo_movimiento')
                    ->required()
                    ->options([
                        'ENTRADA' => 'ENTRADA',
                        'SALIDA' => 'SALIDA',
                        'TRASFERENCIA' => 'TRASFERENCIA',
                    ]),
                DatePicker::make('fecha_movimiento')
                    ->required(),
                Select::make('proveedor_id')
                    ->relationship('proveedor', 'proveedor_id')
                    ->label('Proveedor')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array =>
                    Persona::whereRaw("CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) LIKE ?", ["%{$search}%"])
                        ->orWhere('numero_documento', 'like', "%{$search}%")
                        ->get()
                        ->mapWithKeys(fn ($persona) => [$persona->id => $persona->full_name])
                        ->toArray()
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name}"),
                Select::make('almacen_origen_id')
                    ->label('Origen')
                    ->options(Almacen::all()->pluck('nombre', 'id'))
                    ->searchable(),
                Select::make('almacen_destino_id')
                    ->label('Destino')
                    ->options(Almacen::all()->pluck('nombre', 'id'))
                    ->searchable(),
                TableRepeater::make('items')
                    ->relationship('items')
                    ->headers([
                        Header::make('producto_id')->label('Producto')->width('150px'),
                        Header::make('numero_serie')->label('N° Serie')->width('150px'),
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
                            )
                            ->getOptionLabelUsing(function ($value) {
                                $producto = Producto::with('productoAtributos.atributo')->find($value);
                                if (!$producto) return '';

                                $badges = $producto->productoAtributos
                                    ->map(fn ($pa) =>
                                        "<span class='inline-flex items-center gap-x-1 rounded-md px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'>" .
                                        ($pa->atributo?->nombre ?? 'N/A') . ': ' . $pa->valor .
                                        "</span>"
                                    )
                                    ->implode(' ');

                                return $producto->nombre . ($badges ? "<br><div class='mt-1'>{$badges}</div>" : '');
                            }),
//                        TextInput::make('cantidad')
//                            ->numeric()
//                            ->required()
//                            ->minValue(1)
//                            ->columnSpan(1),
                        TextInput::make('numero_serie')
                            ->label('N° Serie')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record?->productoItem?->numero_serie)
                            ->hidden(fn ($operation) => $operation === 'create')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('verBarcode')
                                    ->icon('heroicon-m-qr-code')
                                    ->url(fn ($record) => $record?->productoItem?->id
                                        ? route('producto.bc', ['id' => $record?->productoItem?->id])
                                        : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn ($record) => $record?->productoItem?->id !== null)
                            ),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->defaultItems(1)
                    ->addActionLabel('Agregar producto')
                    ->deleteAction(
                        fn ($action) => $action->requiresConfirmation()
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo_movimiento')
                ->label('Tipo'),
                TextColumn::make('fecha_movimiento')
                    ->label('Fecha')
                    ->date('d/m/Y'),
                TextColumn::make('proveedor_id')->label('Proveedor')
                    ->formatStateUsing(fn ($record) => $record->proveedor->full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('proveedor', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?", ["%{$search}%"])
                                ->orWhere('numero_documento', $search);
                        });
                    }),
                Tables\Columns\TextColumn::make('almacen_origen_id')
                    ->formatStateUsing(fn ($record) => $record->almacenOrigen->nombre)
                    ->label('Origen'),
                Tables\Columns\TextColumn::make('almacen_destino_id')
                    ->formatStateUsing(fn ($record) => $record->almacenDestino->nombre)
                    ->label('Destino'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMovimientos::route('/'),
        ];
    }
}
