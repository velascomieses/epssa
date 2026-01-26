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
            'position' => 'C',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true, // Esto es vital para que no se pase del ancho asignado
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 2,    // Añadimos padding interno al contenedor del código
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 7,    // Un poco más pequeño ayuda a no ensanchar el código
            'stretchtext' => 4
        );

        $this->AddPage();

        // Reducimos el ancho de 76 a 70 para garantizar "Quiet Zones" a los lados
        // Reducimos el ancho de barra a 0.4 para que el Code128 sea más nítido en térmicas
        $this->write1DBarcode(
            $content,
            'C128',
            '',    // X centrado
            5,     // Y un poco más arriba para dar aire
            70,    // Ancho total reducido (deja 5mm a cada lado)
            22,    // Aumentamos un poco el alto (facilita el escaneo rápido)
            0.4,   // Grosor de barra equilibrado
            $style,
            'N'
        );
    }
}

