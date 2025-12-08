<?php

namespace App\Filament\Widgets;

use App\Models\Contrato;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $vigentes = Contrato::query()
                ->where('estado_id', 1)
                ->count();
        $cancelados = Contrato::query()
                ->where('estado_id', 2)
                ->count();
        $donaciones = Contrato::query()
                ->where('tipo_contrato_id', 4)
                ->count();
        return [
            Stat::make('Contratos vigentes', $vigentes),
            Stat::make('Contratos cancelados', $cancelados),
            Stat::make('Donaciones', $donaciones),
        ];
    }
}
