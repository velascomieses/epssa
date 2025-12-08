<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovimientoItemResource\Pages;
use App\Filament\Resources\MovimientoItemResource\RelationManagers;
use App\Models\MovimientoItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovimientoItemResource extends Resource
{
    protected static ?string $model = MovimientoItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel  = 'Ítems';

    protected static ?string $slug = 'inventario-items';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('movimiento_id')
                    ->required()
                    ->maxLength(36),
                Forms\Components\TextInput::make('producto_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cantidad')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('producto_item_id')
                    ->maxLength(36)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                   Tables\Columns\TextColumn::make('producto_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('producto_item_id')
                    ->searchable(),
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
            'index' => Pages\ManageMovimientoItems::route('/'),
        ];
    }
}
