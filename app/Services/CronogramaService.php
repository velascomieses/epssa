<?php

namespace App\Services;

use App\Models\Contrato;
use App\Models\Solicitud;
use Illuminate\Support\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class CronogramaService
{
    public function calcular(int $id): void
    {
            /* Datos iniciales */
            $obj = Contrato::findOrFail($id);

            $total = $obj->total;

            $fini = $obj->fecha_contrato; // fecha inicial
            $fven = $obj->fecha_vencimiento; // fecha vencimiento
            $d = $obj->dia; // dia de pago
            $Nc = $obj->numero_cuotas; // numero de cuotas
            $k = $total - $obj->inicial - $obj->descuento; // total
            $ini = $obj->inicial; // inicial
            $tea = $obj->tea;

            $fini = new \DateTime($fini);
            $fven = new \DateTime($fven);
            $dven = array();
            $f1 = $fini;
            $f2 = $fven;
            for ($i = 1; $i <= $Nc; $i++):
                $dven[$i] = array($f1->format('Y-m-d'), $f2->format('Y-m-d'));
                $f1 = new \DateTime($f2->format('Y-m-d'));
                $f2->modify('last day of next month');
                if (checkdate($f2->format('m'), $d, $f2->format('Y'))):
                    $f2 = new \DateTime(date('Y-m-d', mktime(0, 0, 0, $f2->format('m'), $d, $f2->format('Y'))));
                endif;
            endfor;

            // factor
            $F = 0;
            $f1 = new \DateTime($dven[1][0]);
            for ($i = 1; $i <= $Nc; $i++):
                $f2 = new \DateTime($dven[$i][1]);
                $n1 = $f1->diff($f2)->days;
                $F = $F + (1 / pow((1 + $tea / 100), $n1 / 360));
            endfor;
            $C = round($k / $F, 1);  // cuota

            try {

                $db = DB::connection();
                $db->beginTransaction();

                $conn = $db->getPdo();

                $sql =
                    "INSERT INTO cronograma (contrato_id,cuota,fecha_inicio,fecha_vencimiento,saldo,capital,interes, importe, estado)
                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, 0 )";

                if ($ini > 0 && $obj->excluir_inicial != 1):
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(1, $obj->id, \PDO::PARAM_INT);
                    $stmt->bindValue(2, 0, \PDO::PARAM_INT);
                    $stmt->bindValue(3, $fini->format('Y-m-d'), \PDO::PARAM_STR);
                    $stmt->bindValue(4, $fini->format('Y-m-d'), \PDO::PARAM_STR);
                    $stmt->bindValue(5, $k + $ini, \PDO::PARAM_STR);
                    $stmt->bindValue(6, $ini, \PDO::PARAM_STR);
                    $stmt->bindValue(7, 0, \PDO::PARAM_STR);
                    $stmt->bindValue(8, $ini, \PDO::PARAM_STR);
                    $stmt->execute();
                endif;

                foreach ($dven as $key => $row):
                    $f1 = new \DateTime($row[0]);
                    $f2 = new \DateTime($row[1]);
                    $n = $f2->diff($f1)->days;
                    $i = round((pow((1 + $tea / 100), $n / 360) - 1) * $k, 2);
                    $c = $key;
                    $cap = $C - $i;
                    $cuo = $k + $i;
                    $stmt = $conn->prepare($sql);

                    if ($c == $Nc):

                        $stmt->bindValue(1, $obj->id, \PDO::PARAM_INT);
                        $stmt->bindValue(2, $c, \PDO::PARAM_INT);
                        $stmt->bindValue(3, $f1->format('Y-m-d'), \PDO::PARAM_STR);
                        $stmt->bindValue(4, $f2->format('Y-m-d'), \PDO::PARAM_STR);
                        $stmt->bindValue(5, $k, \PDO::PARAM_STR);
                        $stmt->bindValue(6, $k, \PDO::PARAM_STR);
                        $stmt->bindValue(7, $i, \PDO::PARAM_STR);
                        $stmt->bindValue(8, $cuo, \PDO::PARAM_STR);

                    else:

                        $stmt->bindValue(1, $obj->id, \PDO::PARAM_INT);
                        $stmt->bindValue(2, $c, \PDO::PARAM_INT);
                        $stmt->bindValue(3, $f1->format('Y-m-d'), \PDO::PARAM_STR);
                        $stmt->bindValue(4, $f2->format('Y-m-d'), \PDO::PARAM_STR);
                        $stmt->bindValue(5, $k, \PDO::PARAM_STR);
                        $stmt->bindValue(6, $cap, \PDO::PARAM_STR);
                        $stmt->bindValue(7, $i, \PDO::PARAM_STR);
                        $stmt->bindValue(8, $C, \PDO::PARAM_STR);

                    endif;

                    $stmt->execute();
                    $k = $k - ($C - $i);
                endforeach;

                $db->table('contrato')
                    ->where('id', '=', $id)
                    ->update(['estado_id' => 1, 'updated_at' => Carbon::now(), 'user_audit_id' => Auth::user()->id]);

                $db->commit();

            } catch (\Throwable $e) {
                $db->rollback();
                throw $e;
            }
        }
    public function eliminar(int $contratoId): void
    {
        $id = $contratoId;
        Solicitud::findOrFail($id);
        try {
            DB::beginTransaction();
            DB::table('pago')->where('contrato_id', '=', $id )
                ->update(['estado' => 1, 'user_audit_id' => Auth::user()->id, 'updated_at' => Carbon::now()]);
            /* Eliminar amortizaciones */
            DB::table('amortizacion')->where('contrato_id', '=', $id)->delete();
            /* Eliminar cronograma */
            DB::table('cronograma')->where('contrato_id', '=', $id )->delete();
            /* Actualizar estado solicitud */
            DB::table('contrato')->where('id', '=', $id )->update([
                'estado_id' => null, 'user_audit_id' => Auth::user()->id, 'updated_at' => Carbon::now()
            ]);
            DB::commit();
        } catch (\Trowable $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function getCurrent($id, $fecha)
    {
        $result = DB::select('CALL sp_current_schedule(?, ?)', [$id, $fecha]);
        foreach ($result as $item) {
            $item->total = number_format($item->importe, 2, '.', '');
            $item->interes = number_format($item->interes, 2, '.', '');
            $item->mora = number_format($item->mora, 2, '.', '');
            $item->inicio = Carbon::parse($item->fecha_inicio)->format('d/m/Y');
            $item->vencimiento = Carbon::parse($item->fecha_vencimiento)->format('d/m/Y');
        }
        return $result;
    }

}
