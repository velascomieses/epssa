<p><b>HISTORIAL DE OTROS PAGOS</b></p>
<p></p>
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
            <td colspan="3">{{ $contrato->rolTitular->full_name }}</td>
        </tr>
        <tr>
            <td>DIRECCIÓN</td>
            <td colspan="3" >{{ $contrato->rolTitular->direccion1 }} {{ $contrato->rolTitular->ubigeo?->full_name  }}</td>
        </tr>
        <tr>
            <td>TELÉFONO</td>
            <td>{{ $contrato->rolTitular->telefono }}</td>
        </tr>
    </tbody>
</table>
<p></p>
<table border="1" cellpadding="2" style="width: 100%; border-collapse: collapse; border: #4a5568;">
    <thead>
    <tr>
        <th>CÓDIGO</th>
        <th>FECHA</th>
        <th>NÚM. RECIBO</th>
        <th>NÚM. COMP.</th>
        <th>CONCEPTO</th>
        <th>IMPORTE</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_importe = 0;
    ?>
    @foreach ($pagos as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->fecha_emision }}</td>
            <td>{{ $item->recibo }}</td>
            <td>{{ $item->serie_numero }}</td>
            <td>{{ $item->producto?->nombre }}</td>
            <td>{{ sprintf("%.2f",$item->importe) }}</td>
        </tr>
            <?php
            $total_importe += $item->importe;
            ?>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4" style="text-align: right;">TOTALES</td>
        <td>{{ sprintf("%.2f",$total_importe) }}</td>
    </tr>
    </tfoot>
</table>
