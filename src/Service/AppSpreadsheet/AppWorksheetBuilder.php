<?php

namespace App\Service\AppSpreadsheet;

class AppWorksheetBuilder
{
    public function __construct(
        private AppWorksheetDataHelper $dataHelper,
        private AppWorksheetDataValidationHelper $dataValidationHelper,
        private AppWorksheetStyleHelper $styleHelper,
    ) {}

    /**
     * @param AppWorksheet $worksheet
     * @param string $title
     * @param int $cols Number of table columns
     * @return void
     * @throws AppWorksheetException
     */
    public function setHeader(AppWorksheet $worksheet, string $title, int $cols): void
    {
        $coordinates = [1, 1];
        $worksheet->mergeCells(array_merge($coordinates, [$cols, 1]));
        $this->dataHelper->setHeaderData($worksheet, $coordinates, $title);
        $this->styleHelper->setHeaderStyle($worksheet, $coordinates);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $rowIndex
     * @return void
     * @throws AppWorksheetException
     */
    public function setColumnsHeading(AppWorksheet $worksheet, array $columns, int $rowIndex): void
    {
        $this->dataHelper->setColumnHeadingData($worksheet, $columns, $rowIndex);
        $this->styleHelper->setColumnHeadingStyle($worksheet, $columns, $rowIndex);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param array $data
     * @param int $baseRow
     * @param Column[] $columns
     * @return void
     */
    public function setData(AppWorksheet $worksheet, array $data, int $baseRow, array $columns)
    {
        $this->dataHelper->setRowsData($worksheet, $baseRow, $data);
        $this->styleHelper->setDataRowsStyle($worksheet, $baseRow, count($data), $columns);
        $this->dataHelper->setRowsTotalData($worksheet, $columns, $baseRow, count($data));
        $this->styleHelper->setTotalDataRowStyle($worksheet, $baseRow + count($data), $columns);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $baseRow
     * @param int $nbRows
     * @return void
     */
    public function setDataValidation(AppWorksheet $worksheet, array $columns, int $baseRow, int $nbRows)
    {
        $this->dataValidationHelper->setValidationData($worksheet, $columns, $baseRow, $nbRows);
    }
}
