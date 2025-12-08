<?php

namespace App\Filament\Widgets;

use App\Models\Producto;
use App\Models\Pago;
use App\Models\ResumenPago;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PaymentsSummaryByPeriodAndConcept extends BaseWidget
{
    protected static ?string $heading = 'Resumen de pagos';
    protected array|string|int $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query( ResumenPago::with('producto') )
            ->columns([
                TextColumn::make('year')->label('Año')->sortable(),
                TextColumn::make('month_name')->label('Mes')->sortable(),
                TextColumn::make('producto_id')
                    ->label('Producto')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state == 0) {
                            return 'Pagos por cuotas';
                        }
                        return $record->producto?->nombre ?? 'N/A';
                    })
                    ->sortable(),
                TextColumn::make('total')->label('Total'),
            ])
            ->filters([
                SelectFilter::make('producto_id')
                    ->label('Producto')
                    ->options(function () {
                        return [0 => 'Pagos por cuotas'] +
                            Producto::all()->pluck('nombre', 'id')->toArray();
                    }),
                SelectFilter::make('year')
                    ->label('Año')
                    ->options(Pago::query()
                        ->select(DB::raw('EXTRACT(YEAR FROM fecha_emision) as year'))
                        ->distinct()
                        ->orderBy(DB::raw('EXTRACT(YEAR FROM fecha_emision)'), 'asc')
                        ->pluck('year', 'year')),
                SelectFilter::make('month')
                    ->label('Mes')
                    ->options([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre',
                    ]),
            ])
            ->defaultSort('year', 'desc');
    }
    protected function paginateTableQuery(Builder $query): Paginator | CursorPaginator
    {
        return $query->paginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
