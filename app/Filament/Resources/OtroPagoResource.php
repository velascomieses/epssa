<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtroPagoResource\Pages;
use App\Filament\Resources\OtroPagoResource\RelationManagers;
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

class OtroPagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Pagos';

    protected static ?string $navigationLabel = 'Otros pagos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            'index' => Pages\ManageOtroPagos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tipo_ingreso', '=', 2);
    }
}
