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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Awcodes\TableRepeater\Components\TableRepeater;

class MovimientoResource extends Resource
{
    protected static ?string $model = Movimiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
//                        Header::make('cantidad')->width('50px'),
                        Header::make('numero_serie')->label('N° Serie')->width('150px'),
                    ])
                    ->schema([
                        Select::make('producto_id')
                            ->label('Producto')
                            ->options(Producto::all()->pluck('nombre', 'id'))
                            ->required()
                            ->columnSpan(2),
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
                            ->hidden(fn ($operation) => $operation === 'create'),
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_movimiento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_movimiento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proveedor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('almacen_origen_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('almacen_destino_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMovimientos::route('/'),
        ];
    }
}
