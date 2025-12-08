<h3 style="text-align: center">CONTRATO DE SERVICIOS FUNERARIOS</h3>
<table cellpadding="3" cellspacing="0">
    <tbody>
    <tr>
        <td style="width: 15%">CÓDIGO</td>
        <td style="width: 60%" >{{ $contrato->id }}</td>
        <td style="width: 15%" >CÓD. INTERNO</td>
        <td>{{ $contrato->numero_contrato }}</td>
    </tr>
    <tr>
        <td>FECHA</td>
        <td>{{ Carbon\Carbon::parse($contrato->fecha_contrato)->format('d/m/Y') }}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td style="width: 15%">CONTRATANTE</td>
        <td style="width: 60%" >{{ $contrato->rolTitular?->full_name }}</td>
        <td style="width: 15%" >DNI</td>
        <td>{{ $contrato->rolTitular?->numero_documento }}</td>
    </tr>
    <tr>
        <td>DIRECCIÓN</td>
        <td>{{ $contrato->rolTitular?->direccion }}</td>
        <td>TEL.</td>
        <td>{{ $contrato->rolTitular?->telefono }}</td>
    </tr>
    <tr>
        @php $beneficiario = $contrato->beneficiarios->first(); @endphp
        <td>Q.E.V.F.</td>
        <td>{{ $beneficiario->persona->full_name }}</td>
        <td>DNI</td>
        <td>{{ $beneficiario->persona->numero_documento }}</td>
    </tr>
</table>
<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
    <thead>
        <tr>
            <td colspan="2" style="background-color: #f0f0f0; padding: 8px; font-weight: bold; text-align: center;">
                DETALLE DEL CONTRATO
            </td>
        </tr>
        <tr>
            <th style="width: 70%; text-align: left; padding: 8px; background-color: #e8e8e8; font-weight: bold;">
                DESCRIPCIÓN
            </th>
            <th style="width: 30%; text-align: center; padding: 8px; background-color: #e8e8e8; font-weight: bold;">
                CANTIDAD
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($contrato->productos as $item)
            <tr>
                <td style="text-align: left; padding: 6px; border: 1px solid #000;">
                    {{ $item->producto?->nombre }}
                </td>
                <td style="text-align: center; padding: 6px; border: 1px solid #000;">
                    {{ $item->cantidad }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<h4>Cláusula: Forma de Pago y Consecuencias del Incumplimiento</h4>
<p>
El servicio contratado se brinda bajo la modalidad de pago al contado. No obstante, de manera excepcional y
únicamente a solicitud del Contratante, la empresa podrá aceptar pagos por adelantado.
</p>
<h4>Cláusula Adicional: Condiciones de Pago en Caso de Atención por Seguros</h4>
<p style="text-align: justify" >
En caso el servicio funerario sea solicitado bajo la modalidad de cobertura por un seguro (SOAT, ESSALUD, AFP,
compañía aseguradora u otra entidad similar), el Contratante autoriza expresamente a Empresa de Prestación de
Servicios de Sepelios y Afines S.R.L. – “Funeraria Mello” a efectuar todas las gestiones administrativas,
documentarias y de cobro necesarias ante la entidad aseguradora correspondiente,
para efectos de tramitar el reembolso o pago directo del servicio.
</p>
<p style="text-align: justify">
No obstante, el Contratante reconoce que la obligación de pago no se transfiere a la aseguradora,
sino que esta solo constituye un tercero obligado de forma eventual, sujeto a evaluación, aprobación y
liquidación conforme a sus propias normas internas. En consecuencia, si la cobertura no procediera, fuera denegada,
observada, reducida o aprobada parcialmente, por cualquier causa atribuible o no al asegurado, el Contratante asume
la obligación total e inmediata del pago del servicio funerario contratado, debiendo cancelar el monto íntegro
según las tarifas vigentes y dentro de los plazos establecidos en el presente contrato. Asimismo, el Contratante
declara conocer y aceptar que la responsabilidad final del pago recae sobre él, independientemente del resultado,
demora, observación o contingencias que pudieran presentarse durante el trámite ante la entidad aseguradora.
</p>

