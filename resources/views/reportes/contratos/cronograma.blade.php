<table cellpadding="2" cellspacing="0">
    <tbody>
    <tr>
        <td>CÓDIGO</td>
        <td>{{ $contrato->id }}</td>
        <td>FECHA</td>
        <td>{{ Carbon\Carbon::parse($contrato->fecha_contrato)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td>TITULAR</td>
        <td colspan="3">{{ $contrato->titular->full_name }}</td>
    </tr>
    <tr>
        <td>DIRECCIÓN</td>
        <td colspan="3">{{ $contrato->titular->direccion }} </td>
    </tr>
    <tr>
        <td>TELÉFONO</td>
        <td>{{ $contrato->titular->telefono }}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>NÚM. CUOTAS</td>
        <td>{{ $contrato->numero_cuotas }}</td>
    </tr>
    </tbody>
</table>
<table cellpadding="2" cellspacing="0">
    <thead>
    <tr>
        <th>#</th>
        <th>F. VEN.</th>
        <th>SALDO</th>
        <th>CAPITAL</th>
        <th>INTERES</th>
        <th>CUOTA</th>
        <th>ESTADO</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_capital = 0;
    $total_interes = 0;
    $total_importe = 0;
    ?>
    @foreach ($contrato->cronograma as $item)
        <tr>
            <td>{{ $item->cuota }}</td>
            <td>{{ Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') }}</td>
            <td>{{ sprintf("%.2f",$item->saldo)  }}</td>
            <td>{{ sprintf("%.2f",$item->capital) }}</td>
            <td>{{ sprintf("%.2f",$item->interes) }}</td>
            <td>{{ sprintf("%.2f",$item->importe) }}</td>
            <td>
                @if ($item->estado == 0)
                    PEN
                @else
                    CAN
                @endif
            </td>
        </tr>
            <?php
            $total_capital += $item->capital;
            $total_interes += $item->interes;
            $total_importe += $item->importe;
            ?>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3" style="text-align: right;">TOTALES</td>
        <td>{{ sprintf("%.2f",$total_capital) }}</td>
        <td>{{ sprintf("%.2f",$total_interes) }}</td>
        <td>{{ sprintf("%.2f",$total_importe) }}</td>
        <td></td>
    </tr>
    </tfoot>
</table>


