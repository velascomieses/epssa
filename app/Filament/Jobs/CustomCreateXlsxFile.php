<?php

namespace App\Filament\Jobs;

use App\Contracts\XlsxCustomizer;
use Filament\Actions\Exports\Jobs\CreateXlsxFile as FilamentCreateXlsxFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class CustomCreateXlsxFile extends FilamentCreateXlsxFile implements XlsxCustomizer
{
    /**
     * Maneja la creación del archivo XLSX con personalizaciones
     */
    public function handle(): void
    {
        $disk = $this->export->getFileDisk();

        $writer = app(Writer::class);
        $writer->openToFile($temporaryFile = tempnam(sys_get_temp_dir(), $this->export->file_name));

        $csvDelimiter = $this->exporter::getCsvDelimiter();

        $headers = [];
        $rows = [];

        // Función para leer archivos CSV en memoria
        $readRowsFromFile = function (string $file) use ($csvDelimiter, $disk, &$headers, &$rows) {
            $csvReader = CsvReader::createFromStream($disk->readStream($file));
            $csvReader->setDelimiter($csvDelimiter);
            $csvResults = (new Statement)->process($csvReader);

            $fileRows = [];
            foreach ($csvResults->getRecords() as $row) {
                $fileRows[] = $row;
            }

            // Determinar si es un archivo de encabezados o datos
            if (str($file)->endsWith('headers.csv')) {
                $headers = $fileRows;
            } else {
                $rows = array_merge($rows, $fileRows);
            }
        };

        // Leer encabezados
        $readRowsFromFile($this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv');
        // Leer datos
        foreach ($disk->files($this->export->getFileDirectory()) as $file) {
            if (str($file)->endsWith('headers.csv') || !str($file)->endsWith('.csv')) {
                continue;
            }
            $readRowsFromFile($file);
        }

        $writeRowsFromFile = function (string $file, ?Style $style = null) use ($csvDelimiter, $disk, $writer) {
            $csvReader = CsvReader::createFromStream($disk->readStream($file));
            $csvReader->setDelimiter($csvDelimiter);
            $csvResults = (new Statement)->process($csvReader);

            foreach ($csvResults->getRecords() as $row) {
                $writer->addRow(Row::fromValues($row, $style));
            }
        };

        $cellStyle = $this->exporter->getXlsxCellStyle();

//        $writeRowsFromFile(
//            $this->export->getFileDirectory() . DIRECTORY_SEPARATOR . 'headers.csv',
//            $this->exporter->getXlsxHeaderCellStyle() ?? $cellStyle,
//        );

//        foreach ($disk->files($this->export->getFileDirectory()) as $file) {
//            if (str($file)->endsWith('headers.csv')) {
//                continue;
//            }
//
//            if (! str($file)->endsWith('.csv')) {
//                continue;
//            }
//
//            $writeRowsFromFile($file, $cellStyle);
//        }
        // Llamada a la función de personalización
        $this->customize($writer, $headers, $rows);
        $writer->close();

        $disk->putFileAs(
            $this->export->getFileDirectory(),
            new File($temporaryFile),
            "{$this->export->file_name}.xlsx",
            Filesystem::VISIBILITY_PRIVATE,
        );

        unlink($temporaryFile);
    }
    /**
     * Implementación por defecto para personalizar el XLSX
     */
    public function customize(Writer $writer, array $headers, array $rows): void
    {

    }
}
