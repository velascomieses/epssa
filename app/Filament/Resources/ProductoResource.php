<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Awcodes\TableRepeater\Components\TableRepeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Configuraciones';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('precio_unitario')
                    ->default(null),
                Toggle::make('es_serializado')
                    ->label('Serializable')
                    ->default(false),
                TableRepeater::make('atributos')
                    ->headers([
                        Header::make('atributo_id')->label('Atributo'),
                        Header::make('valor')->label('Valor'),
                    ])
                    ->relationship('productoAtributos')
                    ->schema([
                        Select::make('atributo_id')
                            ->label('Atributo')
                            ->options(\App\Models\Atributo::pluck('nombre', 'id'))
                            ->required()
                            ->searchable()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                        TextInput::make('valor')
                            ->label('Valor')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->defaultItems(0)
                    ->columnSpanFull()
                    ->addActionLabel('Agregar atributo')
                    ->itemLabel(fn (array $state): ?string =>
                        \App\Models\Atributo::find($state['atributo_id'])?->nombre ?? 'Nuevo atributo'
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('atributos')
                    ->label('Atributos')
                    ->badge()
                    ->getStateUsing(function (Producto $record) {
                        return $record->productoAtributos()
                            ->with('atributo')
                            ->get()
                            ->map(function ($productoAtributo) {
                                return ($productoAtributo->atributo?->nombre ?? 'N/A') . ': ' . $productoAtributo->valor;
                            })
                            ->toArray();
                    }),
                TextColumn::make('precio_unitario')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        try {
                            $record->delete();
                            Notification::make()
                                ->title('Registro eliminado con éxito.')
                                ->success()
                                ->send();
                        } catch (QueryException $exception) {
                            Notification::make()
                                ->title('Error al eliminar.')
                                ->body('No se puede eliminar este registro porque está relacionado con otros datos.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation(),
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
            'index' => Pages\ManageProductos::route('/'),
        ];
    }
}
