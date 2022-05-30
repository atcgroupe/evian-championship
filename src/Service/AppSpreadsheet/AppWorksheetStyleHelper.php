<?php

namespace App\Service\AppSpreadsheet;

class AppWorksheetStyleHelper
{
    /**
     * @param AppWorksheet $worksheet
     * @param array $coordinates [int $col, int $row]
     * @return void
     */
    public function setHeaderStyle(AppWorksheet $worksheet, array $coordinates): void
    {
        $worksheet->setCellVerticalAlignment($coordinates, CellAlignment::VERTICAL_CENTER);
        $worksheet->setCellHorizontalAlignment($coordinates, CellAlignment::HORIZONTAL_CENTER);
        $worksheet->setCellColor($coordinates, 'B40F69');
        $worksheet->setCellFontSize($coordinates, 20);
        $worksheet->setRowHeight(1, 50);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $rowIndex
     * @return void
     * @throws AppWorksheetException
     */
    public function setColumnHeadingStyle(AppWorksheet $worksheet, array $columns, int $rowIndex): void
    {
        $coordinates = [1, $rowIndex, count($columns), $rowIndex];
        $worksheet->setCellsBgColor($coordinates, 'B40F69');
        $worksheet->setCellsColor($coordinates, 'FFFFFF');
        $worksheet->setCellsFontSize($coordinates, 14);
        $worksheet->setCellsBordersColor($coordinates, CellColor::COLOR_WHITE);
        $worksheet->setCellsBordersWeight($coordinates, CellBorder::THIN);
        $worksheet->setRowHeight($rowIndex, 40);
        $worksheet->setCellsVerticalAlignment($coordinates, CellAlignment::VERTICAL_CENTER);
        $this->setColumnsHorizontalAlignment($worksheet, $columns, $rowIndex, $rowIndex);
        $this->setColumnsWidth($worksheet, $columns);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param int $rowIndex First row index
     * @param int $countDataRows Number of rows of data
     * @param Column[] $columns
     * @return void
     */
    public function setDataRowsStyle(AppWorksheet $worksheet, int $rowIndex, int $countDataRows, array $columns): void
    {
        $endRowIndex = $rowIndex + $countDataRows;
        for ($i = $rowIndex; $i <= $endRowIndex; $i ++) {
            $worksheet->setRowHeight($i, 30);
        }

        $this->setColumnsHorizontalAlignment($worksheet, $columns, $rowIndex, $endRowIndex);
        $worksheet->setCellsVerticalAlignment([1, $rowIndex, count($columns), $endRowIndex], CellAlignment::VERTICAL_CENTER);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param int $rowIndex
     * @param Column[] $columns
     * @return void
     */
    public function setTotalDataRowStyle(AppWorksheet $worksheet, int $rowIndex, array $columns): void
    {
        $coordinates = [1, $rowIndex, count($columns), $rowIndex];
        $worksheet->setCellsColor($coordinates, 'B40F69');
        $worksheet->setCellsFontBold($coordinates);
        $worksheet->setRowHeight($rowIndex, 40);
        $worksheet->setCellsVerticalAlignment($coordinates, CellAlignment::VERTICAL_CENTER);
        $this->setColumnsHorizontalAlignment($worksheet, $columns, $rowIndex, $rowIndex);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $startRowIndex
     * @param int $endRowIndex
     * @return void
     */
    private function setColumnsHorizontalAlignment(
        AppWorksheet $worksheet,
        array $columns,
        int $startRowIndex,
        int $endRowIndex
    ) {
        $col = 1;
        foreach ($columns as $column) {
            $worksheet->setCellsHorizontalAlignment(
                [$col, $startRowIndex, $col, $endRowIndex],
                $column->horizontalAlignment
            );
            $col ++;
        }
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @return void
     */
    private function setColumnsWidth(AppWorksheet $worksheet, array $columns) {
        $col = 1;
        foreach ($columns as $column) {
            $worksheet->setColumnWidth($col, $column->width);
            $col ++;
        }
    }
}
