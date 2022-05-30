<?php

namespace App\Service\JobImport;

use App\Repository\ProductRepository;
use App\Service\AppSpreadsheet\AppSpreadsheet;
use App\Service\AppSpreadsheet\AppWorksheetBuilder;
use App\Service\AppSpreadsheet\AppWorksheetException;
use App\Service\AppSpreadsheet\CellAlignment;
use App\Service\AppSpreadsheet\CellValidation;
use App\Service\AppSpreadsheet\Column;

class JobImportTemplateManager
{
    public function __construct(
        private AppWorksheetBuilder $worksheetBuilder,
        private TemplateColumnsBuilder $columnsBuilder,
    ) {}

    /**
     * @return string
     * @throws AppWorksheetException
     */
    public function generateXlsxJobImportTemplate(): string
    {
        $columns = $this->columnsBuilder->getColumns();
        $spreadsheet = new AppSpreadsheet(JobImportTemplate::WORKSHEET_NAME);
        $worksheet = $spreadsheet->getSheetByTitle(JobImportTemplate::WORKSHEET_NAME);
        $this->worksheetBuilder->setColumnsHeading($worksheet, $columns, 1);
        $this->worksheetBuilder->setDataValidation($worksheet, $columns, 2, 100);

        $temp = tempnam(sys_get_temp_dir(), 'xlsx_template_');
        $filename = $temp . '.xlsx';
        rename($temp, $filename);
        $spreadsheet->saveAsXlsx($filename);

        return $filename;
    }
}
