<?php

namespace App\Pdf;
use TCPDF;
class BC_TCPDF extends TCPDF
{
    public function __construct($orientation = 'L', $unit = 'mm', $format = array(80, 40), $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
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
        $this->SetMargins(2, 2, 2);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->SetAutoPageBreak(false, 2);
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
            'hpadding' => '2',
            'vpadding' => '2',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 10,
            'stretchtext' => 4
        );

        $this->AddPage();

        // Altura del código de barras (15-20mm es estándar)
        $this->write1DBarcode($content, 'C128', 2, '', 76, 20, 0.4, $style, 'N');
    }
}

