<p><b>HISTORIAL DE PAGOS</b></p>
<p></p>
<table cellpadding="4" cellspacing="0" >
    <tbody>
    <tr>
        <td>CÓDIGO</td>
        <td>{{ $contrato->id }}</td>
        <td>FECHA</td>
        <td>{{ Carbon\Carbon::parse($contrato->fecha_contrato)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td>TITULAR</td>
        <td colspan="3">{{ $contrato->rolTitular->full_name }}</td>
    </tr>
    <tr>
        <td>DIRECCIÓN</td>
        <td colspan="3">{{ $contrato->rolTitular->direccion }}</td>
    </tr>
    <tr>
        <td>TELÉFONO</td>
        <td colspan="3">{{ $contrato->rolTitular->telefono }}</td>
    </tr>
    </tbody>
</table>
@php
 // $saldoCapital = $contrato->total - $contrato->descuento;
 $saldoCapital = $contrato->cronograma->sum('capital');
 $amortizacion = $contrato->amortizacion->sum('capital');
@endphp
<p></p>
<table cellpadding="4" cellspacing="0" >
    <tbody>
        <tr>
            <td>SALDO CAPITAL</td>
            <td>{{ sprintf("%.2f", $saldoCapital)}}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>AMORTIZACIÓN CAPITAL</td>
            <td colspan="3" >{{ sprintf("%.2f", $amortizacion ) }}</td>
        </tr>
        <tr>
            <td>SALDO ACTUAL</td>
            <td colspan="3" >{{ sprintf("%.2f", $saldoCapital - $amortizacion)  }}</td>
        </tr>
        <tr>
            <td>NÚM. CUOTAS</td>
            <td colspan="3" >{{ $contrato->num_cuotas }}</td>
        </tr>
    </tbody>
</table>
<p></p>
<table cellpadding="2" cellspacing="0" style="width: 100%; border-collapse: collapse; border: #4a5568;" border="1" >
    <thead>
    <tr>
        <th>CÓDIGO</th>
        <th>FECHA</th>
        <th>NÚM. RECIBO</th>
        <th>NÚM. COMP.</th>
        <th>CUOTA</th>
        <th>CAPITAL</th>
        <th>INTERES</th>
        <th>MORA</th>
        <th>IMPORTE</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_capital = 0;
    $total_interes = 0;
    $total_mora = 0;
    $total_importe = 0;
    ?>
    @foreach ($contrato->amortizacion as $item)
        <tr>
            <td>{{ $item->pago_id }}</td>
            <td>{{ Carbon\Carbon::parse($item->pago->fecha_emision)->format('d/m/Y') }}</td>
            <td>{{ $item->pago->recibo }}</td>
            <td>{{ $item->pago->serie_numero }}</td>
            <td>{{ $item->cuota }}</td>
            <td>{{ sprintf("%.2f",$item->capital)  }}</td>
            <td>{{ sprintf("%.2f",$item->interes) }}</td>
            <td>{{ sprintf("%.2f",$item->mora) }}</td>
            <td>{{ sprintf("%.2f",$item->capital + $item->interes + $item->mora) }}</td>
        </tr>
            <?php
            $total_capital += $item->capital;
            $total_interes += $item->interes;
            $total_mora += $item->mora;
            $total_importe += $item->capital + $item->interes + $item->mora;
            ?>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5" style="text-align: right;">TOTALES</td>
        <td>{{ sprintf("%.2f",$total_capital) }}</td>
        <td>{{ sprintf("%.2f",$total_interes) }}</td>
        <td>{{ sprintf("%.2f",$total_mora) }}</td>
        <td>{{ sprintf("%.2f",$total_importe) }}</td>
    </tr>
    </tfoot>
</table>
