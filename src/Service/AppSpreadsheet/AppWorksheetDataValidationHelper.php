<?php

namespace App\Service\AppSpreadsheet;

class AppWorksheetDataValidationHelper
{
    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $startRow
     * @param int $nbRows
     * @return void
     */
    public function setValidationData(AppWorksheet $worksheet, array $columns, int $startRow, int $nbRows)
    {
        for ($rowIndex = $startRow; $rowIndex <= $nbRows + $startRow; $rowIndex++) {
            $this->setRowDataValidation($worksheet, $columns, $rowIndex);
        }
    }

    /**
     * @param AppWorksheet $worksheet
     * @param Column[] $columns
     * @param int $rowIndex
     * @return void
     * @throws AppWorksheetException
     */
    private function setRowDataValidation(AppWorksheet $worksheet, array $columns, int $rowIndex)
    {
        $colIndex = 'A';
        foreach ($columns as $column) {
            switch ($column->dataValidation) {
                case CellValidation::LIST:
                    $worksheet->setCellListValidation(
                        $colIndex . $rowIndex,
                        false,
                        'Erreur',
                        'Cette valeur n\'est pas dans la liste',
                        $column->dataValidationList
                    );
                    break;
                case CellValidation::NUMBER:
                    $worksheet->setCellNumberValidation(
                        $colIndex . $rowIndex,
                        false,
                        'Erreur',
                        'Cette cellule n\'accepte que les entiers positifs.',
                    );
                default:
                    break;
            }

            $colIndex ++;
        }
    }
}
