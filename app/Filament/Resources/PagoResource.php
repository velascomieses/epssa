<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Filament\Resources\PagoResource\RelationManagers;
use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Pagos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha_emision'),
                Forms\Components\DatePicker::make('fecha_calculo'),
                Forms\Components\TextInput::make('moneda_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('recibo')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('importe')
                    ->numeric()
                    ->default(null),
                Forms\Components\Toggle::make('estado'),
                Forms\Components\TextInput::make('contrato_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('personal_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('tipo_comprobante_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('operacion')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('oficina_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('tipo_ingreso')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('referencia')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('anulado'),
                Forms\Components\TextInput::make('serie_numero')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('facturacion_id')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->searchable(),
                TextColumn::make('fecha_emision')->label('Fecha')->date('d/m/Y'),
                TextColumn::make('importe')->label('Importe'),
                TextColumn::make('contrato_id')->label('Contrato'),
                TextColumn::make('contrato.titular_id')->label('Titular')
                    ->formatStateUsing(fn ($record) => $record->contrato?->titular?->full_name),
                IconColumn::make('estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->default(0)
                    ->options([
                        '0' => 'Activo',
                        '1' => 'Eliminado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePagos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tipo_ingreso', '=', 1);
    }
}
