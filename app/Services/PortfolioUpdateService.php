<?php

namespace App\Services;

use App\Models\CarteraRiesgo;
use App\Models\Contrato;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PortfolioUpdateService
{
    private function calcularCategoriaRiesgo(int $diasAtraso): string
    {
        return match(true) {
            $diasAtraso <= 8 => 'Normal',
            $diasAtraso <= 30 => 'CPP',
            $diasAtraso <= 60 => 'Deficiente',
            $diasAtraso <= 120 => 'Dudoso',
            default => 'Pérdida'
        };
    }

    public function update()
    {
        try {
            DB::beginTransaction();

            $fechaHoy = now()->startOfDay();
            CarteraRiesgo::query()->delete();

            Contrato::where('estado_id', 1)
                ->with(['cronograma'])
                ->chunk(100, function($contratos) use ($fechaHoy) {
                    $datos = [];

                    foreach ($contratos as $contrato) {
                        $cronograma = $contrato->cronograma;

                        $ultimaCuotaPagada = $cronograma
                            ->where('estado', 1)
                            ->sortByDesc('cuota')
                            ->first();

                        $cuotasVencidas = $cronograma
                            ->where('fecha_vencimiento', '<', $fechaHoy)
                            ->where('estado', 0)
                            ->count();

                        $primeraVencida = $cronograma
                            ->where('fecha_vencimiento', '<', $fechaHoy)
                            ->where('estado', 0)
                            ->sortBy('cuota')
                            ->first();

                        $diasAtraso = $cuotasVencidas > 0 ?
                            Carbon::parse($primeraVencida->fecha_vencimiento)->diffInDays($fechaHoy) :
                            0;

                        $montoCapital = $contrato->cronograma->sum('capital');
                        $montoPendiente = $montoCapital - $contrato->amortizacion->sum('capital');

                        $datos[] = [
                            'id' => Str::uuid(),
                            'contrato_id' => $contrato->id,
                            'fecha_contrato' => $contrato->fecha_contrato,
                            'titular_id' => $contrato->titular_id,
                            'personal_id' => $contrato->personal_id,
                            'dias_atraso' => $diasAtraso,
                            'monto_capital' => $montoCapital,
                            'monto_pendiente' => $montoPendiente,
                            'num_cuotas_vencidas' => $cuotasVencidas,
                            'ultima_fecha_pago' => $ultimaCuotaPagada?->fecha,
                            'categoria_riesgo' => $this->calcularCategoriaRiesgo($diasAtraso),
                            'tipo_contrato_id' => $contrato->tipo_contrato_id,
                            'total_contrato' => $contrato->total,
                            'fecha_evaluacion' => $fechaHoy
                        ];
                    }

                    CarteraRiesgo::insert($datos);
                });
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        } finally {
            // Log the completion of the update
        }
    }
}
