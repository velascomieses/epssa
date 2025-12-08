<?php

namespace App\Pdf;
use TCPDF;
class TK_TCPDF extends TCPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $format = [80, 200], $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        $this->initialize();
    }
    protected function initialize() {
        // Configuración del documento para ticket/voucher
        $this->SetCreator(config('tcpdf.creator'));
        $this->SetAuthor('EPSSA S.A.C.');
        $this->SetTitle('TICKET');
        $this->SetSubject('TICKET');
        $this->SetKeywords('TCPDF, PDF, EPSSA, TICKET');

        // Sin header ni footer
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);

        // Fuente y tamaño para ticket
        $this->SetFont(config('tcpdf.font_name_main', 'helvetica'), '', config('tcpdf.font_size_main', 8));

        // Márgenes mínimos
        $this->SetMargins(2, 2, 2);
        $this->SetAutoPageBreak(true, 2);
    }
    public function printHtml($content) {
        // add a new page
        $this->AddPage();
        $this->SetFont('courier', '', 9); // Fuente monospace
        $this->SetTextColor(50, 50, 50);
        $this->writeHTML($content, true, false, true, false, '');
    }
}
