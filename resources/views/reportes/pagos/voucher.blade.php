<p style="text-align: center">
    <img src="{{ public_path('images/logo_mello.png') }}" alt="Logo" style="width: 100px; height: auto;">
</p>
<table cellpadding="2" cellspacing="0">
    <tbody>
    <tr style="text-align: center">
        <td>FUNERARIA MELLO</td>
    </tr>
    <tr style="text-align: center" >
        <td>E.P.S DE SEPELIO Y AFINES S.R.L.</td>
    </tr>
    <tr style="text-align: center" >
        <td> JR. MATEO PUMACAHUA CDA. 1 INT. 2 PISO CON JR. LEÓN</td>
    </tr>
    <tr style="text-align: center" >
        <td>TARAPOTO - SAN MARTÍN - SAN MARTÍN </td>
    </tr>
    <tr style="text-align: center">
        <td>TEL. 942 021 961 </td>
    </tr>
    </tbody>
</table>
<table>
    <tbody>
    <tr>
        <td></td>
        <td>{{ $pago->created_at }}</td>
    </tr>
    <tr>
        <td style="width: 22%">Código</td>
        <td style="width: 78%" >{{ $pago->id }}</td>
    </tr>
    <tr>
        <td>Fecha</td>
        <td>{{ Carbon\Carbon::parse($pago->fecha_emision)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td>Oficina</td>
        <td>{{ $pago->oficina?->nombre }}</td>
    </tr>
    <tr>
        <td>Contrato</td>
        <td>{{ $pago->contrato?->id }}</td>
    </tr>
    <tr>
        <td>Cliente</td>
        <td>{{ $pago->contrato?->rolTitular?->full_name }}</td>
    </tr>
    <tr>
        <td colspan="2">=======================================</td>
    </tr>
    <tr>
        <td>Concepto</td>
        <td>{{ $pago->producto?->nombre }}</td>
    </tr>
    <tr>
        <td>Importe</td>
        <td>{{$pago->importe}}</td>
    </tr>
    <tr>
        <td>Medio</td>
        <td>{{ $pago->medioPago?->nombre }}</td>
    </tr>
    <tr>
        <td>Ref</td>
        <td>{{ $pago->referencia }}</td>
    </tr>
    <tr>
        <td>Cajero</td>
        <td>{{ $pago->user?->name }}</td>
    </tr>
    </tbody>
</table>

