<?php

namespace App\Filament\Jobs;


use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Writer\XLSX\Writer;

class PaymentsCreateXlsxFile extends CustomCreateXlsxFile
{
    public function customize(Writer $writer, array $headers, array $rows): void
    {
        $boldStyle = new Style();
        $boldStyle->setFontBold();
        // Opcionalmente puedes agregar otros estilos como color o tamaño
        // $boldStyle->setFontColor(Color::BLACK);
        // $boldStyle->setFontSize(12)
        $writer->addRow(Row::fromValues(['REPORTE DE PAGOS', '', '', '', '', '', ''], $boldStyle));
        $writer->addRow(Row::fromValues(['', '', '', '', '', '', '', '']));
        // Fila de encabezados
        $writer->addRow(Row::fromValues($headers[0]));
        $writer->addRows(
            array_map(
                fn ($rowData) => Row::fromValues($rowData),
                $rows
            )
        );
        $total = array_sum(
            array_filter(
                array_column($rows, 7),
                'is_numeric'
            )
        );
        $totalRow = array_fill(0, count($headers[0]), '');
        $totalRow[0] = 'TOTAL';
        $totalRow[7] = sprintf("%.2f", $total);
        $writer->addRow(Row::fromValues($totalRow,$boldStyle));
    }
}
