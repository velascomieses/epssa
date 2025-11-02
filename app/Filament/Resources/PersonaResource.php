<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonaResource\Pages;
use App\Filament\Resources\PersonaResource\RelationManagers;
use App\Models\Persona;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonaResource extends Resource
{
    protected static ?string $model = Persona::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static  ?string $navigationGroup = 'Plataforma';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('primer_apellido')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('segundo_apellido')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('sexo')
                    ->maxLength(1)
                    ->default(null),
                Forms\Components\DatePicker::make('fecha_nacimiento'),
                Forms\Components\Toggle::make('es_empresa'),
                Forms\Components\Toggle::make('es_convenio'),
                Forms\Components\TextInput::make('numero_documento')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('direccion')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('telefono')
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('correo_electronico')
                    ->maxLength(254)
                    ->default(null),
                Forms\Components\TextInput::make('tipo_documento_identidad_id')
                    ->maxLength(2)
                    ->default(null),
                Forms\Components\TextInput::make('tipo_via_id')
                    ->maxLength(2)
                    ->default(null),
                Forms\Components\TextInput::make('ubigeo_id')
                    ->maxLength(6)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primer_apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('segundo_apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sexo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('es_empresa')
                    ->boolean(),
                Tables\Columns\IconColumn::make('es_convenio')
                    ->boolean(),
                Tables\Columns\TextColumn::make('numero_documento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo_electronico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_documento_identidad_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_via_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ubigeo_id')
                    ->searchable(),
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
            'index' => Pages\ManagePersonas::route('/'),
        ];
    }
}
