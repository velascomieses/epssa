<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoItemResource\Pages;
use App\Filament\Resources\ProductoItemResource\RelationManagers;
use App\Models\ProductoItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductoItemResource extends Resource
{
    protected static ?string $model = ProductoItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel  = 'Ítems';

    protected static ?string $slug = 'inventario-items';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('producto_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('numero_serie')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('almacen_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('estado')
                    ->maxLength(50)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto_id')
                    ->label('Producto')
                    ->formatStateUsing(fn ($record) => $record->producto?->nombre )
                    ->searchable(),
                TextColumn::make('almacen_id')
                    ->label('Almacén')
                    ->formatStateUsing(fn ($record) => $record->almacen?->nombre )
                    ->searchable(),
                TextColumn::make('numero_serie')
                    ->label('Número de Serie')
                    ->formatStateUsing(fn ($record) => $record->numero_serie)
                    ->searchable(),
                TextColumn::make('estado')
                    ->label('Estado'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductoItems::route('/'),
        ];
    }
}
