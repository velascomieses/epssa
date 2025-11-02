<?php

namespace App\Http\Controllers\Plataforma;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Pdf\BC_TCPDF;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function verBarCode($id)
    {
        $content = $id;
        $pdf = new BC_TCPDF(config('tcpdf.page_orientation'), config('tcpdf.page_units'), config('tcpdf.page_format'),  true, config('tcpdf.unicode'), false);
        $pdf->printBc($content);
        $pdf->Output('bc.pdf', 'I');
    }
}
