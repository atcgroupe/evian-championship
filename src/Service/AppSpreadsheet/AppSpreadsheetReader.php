<?php

namespace App\Service\AppSpreadsheet;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AppSpreadsheetReader
{
    private Spreadsheet $spreadsheet;

    public function __construct(
        private string $templateFileDir,
    ) {
        $this->loadSpreadsheet();
    }

    private function loadSpreadsheet(): void
    {
        $this->spreadsheet = IOFactory::load($this->templateFileDir . '/jobs.xlsx');
    }

    /**
     * @param string $name
     * @return AppWorksheet
     */
    public function getWorksheet(string $name): AppWorksheet
    {
        return new AppWorksheet($this->spreadsheet->getSheetByName($name));
    }
}
