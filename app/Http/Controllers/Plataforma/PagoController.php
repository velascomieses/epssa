<?php

namespace App\Http\Controllers\Plataforma;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Pdf\TK_TCPDF;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function voucher($id)
    {
        $pago = Pago::findOrFail($id);
        $content = view('reportes.pagos.voucher', compact('pago'))->render();
        $pdf = new TK_TCPDF();
        $pdf->printHtml($content);
        $pdf->Output('voucher.pdf', 'I');
    }
}
