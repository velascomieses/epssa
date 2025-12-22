<?php

namespace App\Contracts;

use OpenSpout\Writer\XLSX\Writer;

interface XlsxCustomizer
{
    public function customize(Writer $writer, array $headers, array $rows): void;
}
