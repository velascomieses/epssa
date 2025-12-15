<?php

namespace App\Http\Controllers\Plataforma;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Pdf\RPT_TCPDF;

class ContratoController extends Controller
{
    public function verCronograma($id)
    {
        $contrato = Contrato::findOrFail($id);
        $content = view('reportes.contratos.cronograma', compact('contrato'))->render();
        $pdf = new RPT_TCPDF(config('tcpdf.page_orientation'), config('tcpdf.page_units'), config('tcpdf.page_format'),  true, config('tcpdf.unicode'), false);
        $pdf->printHtml($content);
        $pdf->Output('cronograma.pdf', 'I');
    }

    public function verOtroPago($id)
    {
        // dd($id);
        $contrato = Contrato::findOrFail($id);
        $pagos = $contrato->pagos()
            ->where('estado',0)
            ->where('tipo_ingreso', 2)->get();
        $content = view('reportes.contratos.otro_pago', compact('contrato', 'pagos'))->render();
        $pdf = new RPT_TCPDF(config('tcpdf.page_orientation'), config('tcpdf.page_units'), config('tcpdf.page_format'),  true, config('tcpdf.unicode'), false);
        $pdf->printHtml($content);
        $pdf->Output('historial_otros_pagos.pdf', 'I');
    }
    public function verHistorialPago($id)
    {
        $contrato = Contrato::findOrFail($id);
        $content = view('reportes.contratos.historial_pago', compact('contrato'))->render();
        $pdf = new RPT_TCPDF(config('tcpdf.page_orientation'), config('tcpdf.page_units'), config('tcpdf.page_format'),  true, config('tcpdf.unicode'), false);
        $pdf->printHtml($content);
        $pdf->Output('historial_pago.pdf', 'I');
    }
    public function verContrato($id)
    {
        $contrato = Contrato::findOrFail($id);
        $content = view('reportes.contratos.contrato', compact('contrato'))->render();
        $pdf = new RPT_TCPDF(config('tcpdf.page_orientation'), config('tcpdf.page_units'), config('tcpdf.page_format'),  true, config('tcpdf.unicode'), false);
        $pdf->printHtml($content);
        $pdf->Output('contrato.pdf', 'I');
    }
}
