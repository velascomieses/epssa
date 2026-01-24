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

        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->SetJPEGQuality(90);
    }
    public function printBc($content)
    {
        $style = array(
            'position' => 'C',      // Centrado
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',   // Padding automático
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,        // Reducido de 10 a 8
            'stretchtext' => 0      // Sin estiramiento del texto
        );

        $this->AddPage();

        // Ajustes recomendados:
        // - Ancho de barra: 0.5-0.6 para mejor legibilidad
        // - Altura: 15-20mm es óptimo
        // - X: centrado automático con position='C'
        $this->write1DBarcode(
            $content,      // Código
            'C128',        // Tipo
            '',            // X (auto con position='C')
            10,            // Y (10mm desde arriba)
            76,            // Ancho
            20,            // Alto
            0.5,           // Ancho de barra (aumentado de 0.4 a 0.5)
            $style,
            'N'
        );
    }
}

