<?php

namespace App\Pdf;
use TCPDF;
class BC_TCPDF extends TCPDF
{
    public function __construct($orientation = 'L', $unit = 'mm', $format = array(50, 25), $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        $this->initialize();
    }

    protected function initialize() {
        $this->SetCreator(config('tcpdf.creator'));
        $this->SetAuthor('EPSSA S.A.C.');
        $this->SetTitle('Código de Barras');

        // Sin header/footer para etiquetas
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);

        // Márgenes mínimos para etiquetas pequeñas
        $this->SetMargins(1, 1, 1);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->SetAutoPageBreak(false, 1);
    }

   public function printBc($content)
    {
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 7,  // Fuente más pequeña para etiqueta chica
            'stretchtext' => 4
        );

        $this->AddPage();

        // Código de barras ajustado a 50mm x 25mm
        $this->write1DBarcode(
            $content,
            'C128',
            2,      // x: 2mm desde la izquierda
            3,      // y: 3mm desde arriba
            46,     // w: 46mm de ancho (50mm - 4mm de márgenes)
            19,     // h: 19mm de alto (deja espacio para el texto)
            0.4,    // module_width: barras ajustadas
            $style,
            'N'
        );
    }
}
