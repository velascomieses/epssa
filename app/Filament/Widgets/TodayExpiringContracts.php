<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContratoResource;
use App\Models\Contrato;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class TodayExpiringContracts extends BaseWidget
{
    protected static ?int $sort = 3;
    protected array|string|int $columnSpan = 'full';

    protected static ?string $heading = 'Contratos que vencen hoy';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('hoy')
                    ->label(function () {
                        return Carbon::now()->format('d/m/Y');
                    })
                    ->badge()
                    ->color('info'),
            ])
            ->query(
                Contrato::query()
                    ->whereHas('cronograma', function ($query) {
                        $query->whereDate('fecha_vencimiento', now());
                    })
                    ->withCount(['cronograma as cuotas_vencidas' => function ($query) {
                        $query->where('fecha_vencimiento', '<', now())
                            ->where('estado', 0);
                    }])
                    ->with(['personal', 'rolTitular', 'cronograma'])
                    ->where('estado_id', 1)
                    ->distinct()
            )
            ->columns([
                TextColumn::make('solicitud_id')
                    ->label('Código')
                    ->sortable()
                    ->searchable()
                    ->openUrlInNewTab()
                    ->url(fn ($record) => ContratoResource::getUrl('view', [
                        'record' => $record->id,
                    ]))
                    ->openUrlInNewTab()
                    ->color('success'),
                TextColumn::make('fecha_contrato')
                    ->label('Fecha de contrato')
                    ->date('d/m/Y'),
                TextColumn::make('rolTitular')
                    ->label('Titular')
                    ->formatStateUsing(fn ($record) => $record->rolTitular->full_name)
                    ->description( fn($record) => "{$record->rolTitular->telefono} | {$record->rolTitular->direccion1}"),
                TextColumn::make('cuotas_vencidas')
                    ->label('Cuotas vencidas')
                    ->sortable(),
            ]);
    }
    protected function paginateTableQuery(Builder $query): Paginator | CursorPaginator
    {
        return $query->paginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
