<?php

namespace App\Service\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Exception as PhpOfficeException;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class XlsxReportHelper
{
    private Spreadsheet $spreadsheet;

    public function __construct(
        private XlsxReportFormatter $formatter,
    ) {}

    public function initializeSpreadsheet(): void
    {
        $this->spreadsheet = new Spreadsheet();
    }

    public function resetSpreadsheet(): void
    {
        unset($this->spreadsheet);
    }

    /**
     * @param string $title
     * @param Column[] $columns
     * @param array $data
     * @return void
     * @throws PhpOfficeException
     */
    public function addWorksheet(string $title, array $columns, array $data): void
    {
        if ($this->spreadsheet->getSheetCount() > 1) {
            $this->spreadsheet->addSheet(new Worksheet(title: $title));
        }

        $this->getSheet($title)->setTitle($title);
        $this->formatter->formatWorksheet($this->getSheet($title), $columns, $data);
    }

    /**
     * @param string $filename
     * @return void
     * @throws WriterException
     */
    public function save(string $filename)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($filename);
    }

    /**
     * @param string $name
     * @return Worksheet
     */
    private function getSheet(string $name): Worksheet
    {
        if ($this->spreadsheet->getSheetCount() === 1) {
            return $this->spreadsheet->getActiveSheet();
        }

        return $this->spreadsheet->getSheetByName($name);
    }
}
