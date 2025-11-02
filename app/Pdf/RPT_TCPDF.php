<?php

namespace App\Pdf;
use TCPDF;
class RPT_TCPDF extends TCPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        $this->initialize();
    }
    protected function initialize() {
        // Configuración del documento
        $this->SetCreator(config('tcpdf.creator'));
        $this->SetAuthor('EPSSA S.A.C.');
        $this->SetTitle('REPORTE');
        $this->SetSubject('REPORTE');
        $this->SetKeywords('TCPDF, PDF, EPSSA, REPORTE');

        // Configuración del header
        $this->SetHeaderData(
            config('tcpdf.header_logo'),
            config('tcpdf.header_logo_width'),
            config('tcpdf.header_title'),
            config('tcpdf.header_string')
        );

        // Configuración de fuentes
        $this->setHeaderFont([config('tcpdf.font_name_main'), '', config('tcpdf.font_size_main')]);
        $this->setFooterFont([config('tcpdf.font_name_data'), '', config('tcpdf.font_size_data')]);

        // Configuración de márgenes
        $this->SetMargins(config('tcpdf.margin_left'), 17);
        $this->SetHeaderMargin(config('tcpdf.margin_header'));
        $this->SetFooterMargin(config('tcpdf.margin_footer'));

        // Configuración de saltos de página
        $this->SetAutoPageBreak(TRUE, config('tcpdf.margin_bottom'));
        $this->setImageScale(config('tcpdf.image_scale_ratio'));
        $tagvs = array(
            'p' => array(
                0 => array('h' => 0, 'n' => 0), // espaciado antes del párrafo
                1 => array('h' => 0, 'n' => 0)  // espaciado después del párrafo
            ),
            'h4' => array(
                0 => array('h' => 0, 'n' => 0), // espaciado antes del párrafo
                1 => array('h' => 0, 'n' => 0)  // espaciado después del párrafo
            )
        );
        $this->setHtmlVSpace($tagvs);
    }
    public function printHtml($content) {
        // add a new page
        $this->AddPage();
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(50, 50, 50);
        $this->writeHTML($content, true, false, true, false, '');
    }
}

