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
    <tr>
        <td style="width: 15%">LUGAR FALLEC.</td>
        <td colspan="3">{{ $contrato->lugar_fallecimiento }}</td>
    </tr>
    <tr>
        <td>LUGAR SEPUL.</td>
        <td colspan="3" >{{ $contrato->ubigeo?->nombre }}</td>
    </tr>
    <tr>
        <td>DIR. VELAT.</td>
        <td colspan="3" >{{ $contrato->direccion_velatorio }}</td>
    </tr>
</table>
&nbsp;<br />
@php
    $productoItem = \App\Models\ProductoItem::where('numero_serie', $contrato->numero_serie)->first();
@endphp

@if($productoItem)
    @php
        $atributos = $productoItem->producto->atributos->map(function($atributo) {
            return $atributo->nombre . ': ' . $atributo->pivot->valor;
        })->implode(' | ');
    @endphp
    <table cellpadding="3" cellspacing="0" border="0" >
        <tbody>
            <tr>
                <td>CATEGORIA DEL SERVICIO: <span style="font-weight: bold;">{{ $contrato->categoria?->nombre }}</span></td>
            </tr>
            <tr>
                <td>{{ $productoItem->producto->nombre }} {{ $atributos }}</td>
            </tr>
            <tr>
                <td>{{ $productoItem->numero_serie }}</td>
            </tr>
        </tbody>
    </table>
@endif
&nbsp;<br />
<h4 style="text-align: center;" >DETALLE DEL CONTRATO</h4>
<table cellpadding="3" cellspacing="0" border="0" >
    <thead>
        <tr>
            <th style="width: 70%; text-align: left; background-color: #e8e8e8; font-weight: bold;">
                DESCRIPCIÓN
            </th>
            <th style="width: 30%; text-align: center; background-color: #e8e8e8; font-weight: bold;">
                CANTIDAD
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($contrato->productos as $item)
            <tr>
                <td style="text-align: left;">
                    {{ $item->producto?->nombre }}
                </td>
                <td style="text-align: center;">
                    {{ $item->cantidad }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
&nbsp;<br />
<h4 style="font-weight: bold;" >CONDICIONES DE PAGO</h4>
<table cellspacing="3">
    <tbody>
        <tr>
            <td>TOTAL</td>
            <td>{{ sprintf("%.2f", $contrato->total) }}</td>
            <td>INICIAL</td>
            <td>{{ sprintf("%.2f", $contrato->inicial) }}</td>
            <td>DESCUENTO</td>
            <td>{{ sprintf("%.2f", $contrato->descuento) }}</td>
        </tr>
        <tr>
            <td>N° DE CUOTAS</td>
            <td colspan="5">{{ $contrato->numero_cuotas }}</td>
        </tr>
    </tbody>
</table>
&nbsp;<br />
<p>
Siento el interviniente {{ $contrato->personal?->full_name }}, identificado con
    {{ $contrato->personal?->tipoDocumentoIdentidad?->nombre }} N° {{ $contrato->personal?->numero_documento }},
quien da conformidad al presente acto y es el responsable de la deuda detallada.
</p>
<h4>Cláusula: Forma de Pago y Consecuencias del Incumplimiento</h4>
<p style="text-align: justify">
El servicio contratado se brinda bajo la modalidad de pago al contado. No obstante, de manera excepcional y únicamente
a solicitud del Contratante, la empresa podrá aceptar pagos por adelantado. En caso el Contratante no hubiera cancelado
la totalidad del monto pactado por el servicio, se establece que la última cuota vencerá indefectiblemente a los cinco
(05) días naturales posteriores al fallecimiento de la persona para quien se destinó el servicio fúnebre. Vencido dicho
plazo sin haberse efectuado el pago total, y en aplicación de lo dispuesto por los artículos 1242° y 1243° del Código
Civil, así como por los artículos 51° y 52° de la Ley Orgánica del Banco Central de Reserva del Perú, la deuda pendiente
generará: intereses compensatorios, calculados a la tasa fijada por el Banco Central de Reserva del Perú (BCRP), aplicable
desde el día siguiente del vencimiento; e intereses moratorios, equivalentes al cinco por ciento (5%) adicional, por
cada mes o fracción aplicable, hasta la cancelación total de la deuda.
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
<h4>Cláusula: Responsabilidad por daños o pérdida de bienes</h4>
<p style="text-align: justify">En caso de daño, deterioro o pérdida total o parcial de la capilla ardiente y/o de cualquiera de los materiales,
    equipos o accesorios proporcionados por LA EMPRESA para el armado y desarrollo del velatorio, ocasionados por acción
    u omisión del EL CONTRATANTE, sus familiares, invitados o terceros bajo su responsabilidad, EL CONTRATANTE se obliga
    a asumir íntegramente los costos de reparación o reposición del bien afectado, según corresponda, de acuerdo con la
    valorización que determine LA EMPRESA.</p>
<h4>Cláusula: Pagos no autorizados al personal</h4>
<p style="text-align: justify" >LA EMPRESA no se hará responsable por pagos, dádivas, propinas o cualquier otro desembolso
    económico que EL CONTRATANTE, sus familiares o terceros realicen de manera directa al personal operativo o administrativo,
    cuando dichos pagos no se encuentren expresamente contemplados en el presente contrato y/o no cuenten con el respectivo
    comprobante de pago emitido por LA EMPRESA.</p>
<p>Cualquier pago efectuado sin la autorización previa y escrita de LA EMPRESA será considerado ajeno al servicio
    contratado, no generando derecho a reembolso, descuento, compensación ni reclamo alguno contra LA EMPRESA.</p>
&nbsp;<br />
&nbsp;<br />
<table cellpadding="3" border="0">
    <tbody>
        <tr>
            <td style="width: 22%; text-align: center;">
                &nbsp;<br />
                REPRESENTANTE LEGAL
            </td>
            <td style="width: 35%; text-align: center;">
                {{ $contrato->personal?->full_name }}<br />
                INTERVINIENTE
            </td>
            <td style="width: 35%; text-align: center;">
                {{ $contrato->rolTitular?->full_name }}<br />
                CONTRATANTE
            </td>
            <td style="width: 8%; text-align: center;">
                &nbsp;<br />
                AVAL
            </td>
        </tr>
    </tbody>
</table>
