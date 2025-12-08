<?php

namespace App\Pdf;
use TCPDF;
class BC_TCPDF extends TCPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $format = array(50, 30), $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
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

        // Márgenes mínimos para etiquetas
        $this->SetMargins(3, 3, 3);
        $this->SetAutoPageBreak(false);
    }
    public function printBc($content)
    {
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false, // Sin borde para etiquetas
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 9,
            'stretchtext' => 4
        );

        $this->AddPage();

        // Altura del código de barras (15-20mm es estándar)
        $this->write1DBarcode($content, 'C128', '', '', '', 15, 0.4, $style, 'N');
    }
}

