<?php

namespace App\Filament\Widgets;

use App\Filament\Exports\RiskPortfolioExporter;
use App\Filament\Resources\ContratoResource;
use App\Models\CarteraRiesgo;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
class RiskPortfolio extends BaseWidget
{
    protected static ?int $sort = 2;
    protected array|string|int $columnSpan = 'full';

    protected static ?string $heading = 'Cartera de riesgo';

    public function table(Table $table): Table
    {
        $query = CarteraRiesgo::with(['titular']);
        return $table
            ->headerActions([
                Tables\Actions\Action::make('normal')
                    ->label('Normal (max. 8 días)')
                    ->badge()
                    ->tooltip('Atraso máximo de 8 días calendarios')
                    ->color('success'),
                Tables\Actions\Action::make('cpp')
                    ->label('CPP (9-30 días)')
                    ->badge()
                    ->tooltip('Con problemas potenciales. Atrasos entre 9 a 30 días')
                    ->extraAttributes(['style' => 'background-color: rgba(131, 196, 28, 0.7); color: #000000']),
                Tables\Actions\Action::make('deficiente')
                    ->label('Deficiente (31-60 días)')
                    ->badge()
                    ->tooltip('Atrasos entre 31 a 60 días')
                    ->color('warning'),
                Tables\Actions\Action::make('dudoso')
                    ->label('Dudoso (61-120 días)')
                    ->badge()
                    ->tooltip('Atrasos entre 61 a 120 días')
                    ->extraAttributes(['style' => 'background-color: rgba(237, 106, 17, 0.7); color: #000000']),
                Tables\Actions\Action::make('perdida')
                    ->label('Pérdida (>120 días)')
                    ->badge()
                    ->tooltip('Atrasos de más de 120 días')
                    ->color('danger'),
                Tables\Actions\Action::make('fecha_evaluacion')
                    ->label(function () {
                        return Carbon::parse(CarteraRiesgo::first()->fecha_evaluacion)->format('d/m/Y');
                    })
                    ->badge()
                    ->color('info'),
                ExportAction::make()
                    ->label('Exportar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(RiskPortfolioExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->maxRows(25000)
            ])
            ->query($query)
            ->columns([
                TextColumn::make('contrato_id')
                    ->label('Código')
                    ->sortable()
                    ->url(fn ($record) => ContratoResource::getUrl('view', [
                        'record' => $record->contrato_id,
                    ]))
                    ->openUrlInNewTab()
                    ->searchable()
                    ->color('success'),
                TextColumn::make('fecha_contrato')
                    ->label('Fecha contrato')
                    ->date('d/m/Y'),
                TextColumn::make('tipo_contrato_id')
                    ->label('Tipo')
                    ->formatStateUsing(fn($record)=> $record->tipoContrato->nombre),
                TextColumn::make('titular_id')
                    ->label('Titular')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('titular', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?", ["%{$search}%"])
                                ->orWhere('numero_documento', "$search");
                        });
                    })
                    ->formatStateUsing(function ($record) {
                        return $record->titular->full_name;
                    }),
                TextColumn::make('categoria_riesgo')
                    ->label('Categoría'),
                TextColumn::make('dias_atraso')
                    ->label('Días de atraso')
                    ->sortable(),
                TextColumn::make('monto_capital')
                    ->label('Total capital'),
                TextColumn::make('monto_pendiente')
                    ->label('Capital pendiente'),
                TextColumn::make('num_cuotas_vencidas')
                    ->label('Cuotas vencidas'),

            ])
            ->filters([
                SelectFilter::make('categoria_riesgo')
                    ->options([
                        'Normal' => 'Normal',
                        'CPP' => 'CPP',
                        'Deficiente' => 'Deficiente',
                        'Dudoso' => 'Dudoso',
                        'Pérdida' => 'Pérdida',
                    ])
//                    ->label('Categoría'),
//                SelectFilter::make('tipo_solicitud')
//                    ->options([
//                        'F' => 'N. Futura',
//                        'I' => 'N. Inmediata',
//                    ])
//                    ->label('Tipo'),
//                SelectFilter::make('consejero_id')
//                    ->relationship(
//                        'consejero',
//                        'nombres'
//                    )
//                    ->getOptionLabelFromRecordUsing(function ($record) {
//                        return $record->nombres . ' ' . $record->apellido1 . ' ' . $record->apellido2;
//                    })
//                    ->searchable()
//                    ->preload()
//                    ->label('Consejero'),
            ]);
    }
    protected function paginateTableQuery(Builder $query): Paginator | CursorPaginator
    {
        return $query->paginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
