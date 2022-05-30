<?php

namespace App\Service\AppSpreadsheet;

class AppWorksheetDataHelper
{
    /**
     * @param AppWorksheet $worksheet
     * @param array $coordinates [int $col, int $row]
     * @param string $title
     * @return void
     */
    public function setHeaderData(AppWorksheet $worksheet, array $coordinates, string $title): void
    {
        $worksheet->setCellValue($coordinates, $title);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $rowIndex
     * @return void
     */
    public function setColumnHeadingData(AppWorksheet $worksheet, array $columns, int $rowIndex): void
    {
        $data = [];
        foreach ($columns as $column) {
            $data[] = $column->label;
        }

        $worksheet->setFromArray('A' . $rowIndex, $data);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param int $rowIndex
     * @param array $data
     * @return void
     */
    public function setRowsData(AppWorksheet $worksheet, int $rowIndex, array $data): void
    {
        $worksheet->setFromArray('A' . $rowIndex, $data);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $startRowIndex
     * @param int $countDataRows
     * @return void
     */
    public function setRowsTotalData(AppWorksheet $worksheet, array $columns, int $startRowIndex, int $countDataRows): void
    {
        $endDataRowIndex = $startRowIndex + $countDataRows -1;
        $totalRowIndex = $endDataRowIndex + 1;
        $worksheet->setCellValue([1, $totalRowIndex], 'TOTAL');

        $colIndex = 1;
        $colLetterIndex = 'A';
        foreach ($columns as $column) {
            if ($column->addTotal) {
                $worksheet->setCellValue(
                    [$colIndex, $totalRowIndex],
                    sprintf('=SUM(%s%s:%s%s)', $colLetterIndex, $startRowIndex, $colLetterIndex, $endDataRowIndex)
                );
            }

            $colIndex ++;
            $colLetterIndex ++;
        }
    }
}
