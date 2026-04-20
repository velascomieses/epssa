<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoItemResource\Pages;
use App\Filament\Resources\ProductoItemResource\RelationManagers;
use App\Models\Contrato;
use App\Models\ProductoItem;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\InventoryExporter;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                ->label('Exportar')
                ->exporter(InventoryExporter::class)
                ->icon('heroicon-o-arrow-down-tray')
                ->formats([ExportFormat::Xlsx])
            ])
            ->columns([
                TextColumn::make('producto_id')
                    ->label('Producto')
                    ->formatStateUsing(function ($record) {
                        $nombre = $record->producto?->nombre ?? 'N/A';
                        $badges = $record->producto?->productoAtributos
                            ->map(fn ($pa) =>
                                "<span class='inline-flex items-center gap-x-1 rounded-md px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'>" .
                                ($pa->atributo?->nombre ?? 'N/A') . ': ' . $pa->valor .
                                "</span>"
                            )
                            ->implode(' ') ?? '';

                        return $nombre . ($badges ? " {$badges}" : '');
                    })
                    ->html()
                    ->searchable(),
                TextColumn::make('proveedor_id')->label('Proveedor')
                    ->formatStateUsing(fn ($record) => $record->proveedor->full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('proveedor', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?", ["%{$search}%"])
                                ->orWhere('numero_documento', $search);
                        });
                    }),
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
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'DISPONIBLE' => 'DISPONIBLE',
                        'VENDIDO' => 'VENDIDO',
                    ]),
                SelectFilter::make('almacen_id')
                    ->label('Almacén')
                    ->relationship('almacen', 'nombre'),
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
