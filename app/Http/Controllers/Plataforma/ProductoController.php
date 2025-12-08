<?php

namespace App\Http\Controllers\Plataforma;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\ProductoItem;
use App\Pdf\BC_TCPDF;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function verBarCode($id)
    {
        $productoItem = ProductoItem::findOrFail($id);
        $content = $productoItem->numero_serie;
        $pdf = new BC_TCPDF();
        $pdf->printBc($content);
        $pdf->Output('bc.pdf', 'I');
    }
}
