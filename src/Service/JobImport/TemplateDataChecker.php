<?php

namespace App\Service\JobImport;

use App\Service\AppSpreadsheet\AppSpreadsheetReader;
use App\Service\AppSpreadsheet\AppWorksheet;
use App\Service\AppSpreadsheet\Column;

class TemplateDataChecker
{
    /**
     * @var Column[]  $templateColumns
     */
    private array $templateColumns;

    /**
     * @var AbstractColumnConstraint[] $templateColumnsConstraints
     */
    private array $templateColumnsConstraints;

    public function __construct(
        private AppSpreadsheetReader $spreadsheetReader,
        private ConstraintDataChecker $dataChecker,
        TemplateColumnsBuilder $columnsBuilder,
    ) {
        $this->templateColumns = $columnsBuilder->getColumns();
        $this->templateColumnsConstraints = $columnsBuilder->getColumnConstraints();
    }

    /**
     * @return DataReport|null
     */
    public function checkData(): DataReport|null
    {
        $rowIndex = 2;
        $worksheet = $this->spreadsheetReader->getWorksheet(JobImportTemplate::WORKSHEET_NAME);

        if (!$this->hasData($worksheet, $rowIndex)) {
            return null;
        }

        $rowsCount = 0;
        $validRowsCount = 0;
        $invalidRowsCount = 0;
        $invalidRowsReports = [];
        $jobRefs = [];

        do {
            $rowViolations = $this->getRowDataConstraintViolations(
                $worksheet->getRowCellsValue('A', 'N', $rowIndex)
            );

            if ($rowViolations === null) {
                $validRowsCount++;
            }

            if (is_array($rowViolations)) {
                $invalidRowsCount ++;
                $invalidRowsReports[] = new InvalidRowReport($rowIndex, $rowViolations);
            }

            $jobRefs[] = $worksheet->getCellValue([1, $rowIndex]);
            $rowIndex ++;
            $rowsCount ++;

        } while ($this->hasData($worksheet, $rowIndex) === true);

        return new DataReport(
            $rowsCount,
            $validRowsCount,
            $invalidRowsCount,
            $invalidRowsReports,
            $this->getRefDuplication($jobRefs),
        );
    }

    /**
     * @param AppWorksheet $worksheet
     * @param int $rowIndex
     * @return bool
     */
    private function hasData(AppWorksheet $worksheet, int $rowIndex): bool
    {
        return null !== $worksheet->getCellValue([1, $rowIndex]);
    }

    /**
     * @param array $rowValues
     * @return array|null
     */
    private function getRowDataConstraintViolations(array $rowValues): array|null
    {
        $result = [];
        $colIndex = 1;
        foreach ($rowValues as $cellValue) {
            $check = $this->dataChecker->check(
                $this->templateColumns[$colIndex],
                $this->templateColumnsConstraints[$colIndex],
                $cellValue
            );

            if ($check !== true) {
                $result[] = $check;
            }

            $colIndex ++;
        }

        return empty($result) ? null : $result;
    }

    /**
     * @param string[] $jobRefs
     * @return RefDuplication[]|null
     */
    private function getRefDuplication(array $jobRefs): array|null
    {
        $countEntries = array_count_values($jobRefs);
        $duplications = [];

        foreach ($countEntries as $countEntryName => $countEntry) {
            if ($countEntry > 1) {
                $duplications[] = new RefDuplication($countEntryName, $countEntry);
            }
        }

        return empty($duplications) ? null : $duplications;
    }
}
