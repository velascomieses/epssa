<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CementerioResource\Pages;
use App\Filament\Resources\CementerioResource\RelationManagers;
use App\Models\Cementerio;
use App\Models\Ubigeo;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;

class CementerioResource extends Resource
{
    protected static ?string $model = Cementerio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Configuraciones';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->maxLength(245)
                    ->required()
                    ->default(null),
                Select::make('ubigeo_id')
                    ->label('Ubigeo')
                    ->searchable()
                    ->required()
                    ->getSearchResultsUsing(function (string $search) {
                        return Ubigeo::query()
                            ->distritos()
                            ->where('nombre', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($record) => [
                                $record->id => $record->full_name
                            ]);
                    })
                    ->getOptionLabelUsing(fn ($value) => Ubigeo::find($value)?->full_name),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ubigeo.nombre')
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
            'index' => Pages\ManageCementerios::route('/'),
        ];
    }
}
