<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalResource\Pages;
use App\Filament\Resources\PersonalResource\RelationManagers;
use App\Models\Personal;
use App\Models\TipoDocumentoIdentidad;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rules\Unique;

class PersonalResource extends Resource
{
    protected static ?string $model = Personal::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Configuraciones';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Personal';
    protected static ?string $slug = 'personal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tipo_documento_identidad_id')
                    ->label('Tipo de documento')
                    ->options(TipoDocumentoIdentidad::all()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('numero_documento')
                    ->label('N° Documento')
                    ->default(null)
                    ->rules([
                        'required',
                        'max:15',
                    ])
                    ->unique(
                        column: 'numero_documento',
                        modifyRuleUsing: function (Unique $rule, Get $get){
                            return $rule->where('tipo_documento_identidad_id', $get('tipo_documento_identidad_id'));
                        },
                        ignoreRecord: true
                    ),
                TextInput::make('nombre')
                    ->maxLength(50)
                    ->default(null),
                TextInput::make('primer_apellido')
                    ->maxLength(50)
                    ->default(null),
                TextInput::make('segundo_apellido')
                    ->maxLength(50)
                    ->default(null),
                Toggle::make('estado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Nombres y apellidos')
                    ->formatStateUsing(fn ($record) => "{$record->nombre} {$record->primer_apellido} {$record->segundo_apellido}")
                    ->searchable(),
                TextColumn::make('tipoDocumentoIdentidad.nombre')
                    ->label('Tipo Doc.'),
                TextColumn::make('numero_documento')
                    ->label('N° Documento')
                    ->searchable(),
                Tables\Columns\IconColumn::make('estado')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->default(1)
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
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
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePersonals::route('/'),
        ];
    }
}
