<?php

namespace App\Service\AppSpreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppWorksheet
{
    public function __construct(
        private Worksheet $worksheet
    ) {}

    /**
     * @return Worksheet
     */
    public function getWorksheet(): Worksheet
    {
        return $this->worksheet;
    }

    /**
     * @param array $coordinates [int $col, int $row]
     * @return mixed
     */
    public function getCellValue(array $coordinates): mixed
    {
        return $this->worksheet->getCell($coordinates)->getValue();
    }

    /**
     * @param string $startCol e.g. 'A'
     * @param string $endCol e.g. 'F'
     * @param int $rowIndex
     * @return array
     */
    public function getRowCellsValue(string $startCol, string $endCol, int $rowIndex): array
    {
        return $this->worksheet->rangeToArray(sprintf('%s%s:%s%s', $startCol, $rowIndex, $endCol, $rowIndex))[0];
    }

    /**
     * @param array $coordinates [int $col, int $row]
     * @param string|int|float $value
     * @return void
     */
    public function setCellValue(array $coordinates, string|int|float $value): void
    {
        $this->worksheet->setCellValue($coordinates, $value);
    }

    /**
     * @param string $startCoordinates e.g. A1
     * @param array $data
     * @return void
     */
    public function setFromArray(string $startCoordinates, array $data): void
    {
        $this->worksheet->fromArray($data, startCell: $startCoordinates);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @return void
     * @throws AppWorksheetException
     */
    public function mergeCells(array $coordinates): void
    {
        try {
            $this->worksheet->mergeCells($coordinates);
        } catch (Exception $exception) {
            throw new AppWorksheetException(
                sprintf('Imposible de fusionner les cellules. %s', $exception->getMessage())
            );
        }
    }

    /**
     * @param int $rowIndex
     * @param int $height
     * @return void
     */
    public function setRowHeight(int $rowIndex, int $height): void
    {
        $this->worksheet->getRowDimension($rowIndex)->setRowHeight($height);
    }

    /**
     * @param int $startRowIndex
     * @param int $endRowIndex
     * @param int $height
     * @return void
     */
    public function setRowsHeight(int $startRowIndex, int $endRowIndex, int $height): void
    {
        for ($i = $startRowIndex; $i <= $endRowIndex; $i++) {
            $this->setRowHeight($i, $height);
        }
    }

    /**
     * @param string $colIndex
     * @param int $width
     * @return void
     */
    public function setColumnWidth(string $colIndex, int $width): void
    {
        $this->worksheet->getColumnDimensionByColumn($colIndex)->setWidth($width);
    }

    /**
     * @param string $startColIndex
     * @param int $endColIndex
     * @param int $width
     * @return void
     */
    public function setColumnsWidth(string $startColIndex, int $endColIndex, int $width): void
    {
        for ($i = $startColIndex; $i <= $endColIndex; $i++) {
            $this->worksheet->getColumnDimensionByColumn($i)->setWidth($width);
        }
    }

    /**
     * @param array $coordinates [int $col, int $row]
     * @param string $alignment
     * @return void
     */
    public function setCellHorizontalAlignment(array $coordinates, string $alignment): void
    {
        $this->worksheet->getStyle(array_merge($coordinates, $coordinates))->getAlignment()->setHorizontal($alignment);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @param string $alignment
     * @return void
     */
    public function setCellsHorizontalAlignment(array $coordinates, string $alignment): void
    {
        $this->worksheet->getStyle($coordinates)->getAlignment()->setHorizontal($alignment);
    }

    /**
     * @param array $coordinates [int $col, int $row]
     * @param string $alignment
     * @return void
     */
    public function setCellVerticalAlignment(array $coordinates, string $alignment): void
    {
        $this->worksheet->getStyle(array_merge($coordinates, $coordinates))->getAlignment()->setVertical($alignment);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @param string $alignment
     * @return void
     */
    public function setCellsVerticalAlignment(array $coordinates, string $alignment): void
    {
        $this->worksheet->getStyle($coordinates)->getAlignment()->setVertical($alignment);
    }

    /**
     * @param array $coordinates [int $col, int $row]
     * @param string $color
     * @return void
     */
    public function setCellColor(array $coordinates, string $color): void
    {
        $this->worksheet->getStyle(array_merge($coordinates, $coordinates))->getFont()->getColor()->setRGB($color);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @param string $color RGB Color e.g. 'FFFFFF'
     * @return void
     */
    public function setCellsColor(array $coordinates, string $color): void
    {
        $this->worksheet->getStyle($coordinates)->getFont()->getColor()->setRGB($color);
    }

    /**
     * @param array $coordinates [int $col, int $row]
     * @param string $color RGB Color e.g. 'FFFFFF'
     * @return void
     */
    public function setCellBgColor(array $coordinates, string $color): void
    {
        $this->worksheet->getStyle(array_merge($coordinates, $coordinates))
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @param string $color RGB Color e.g. 'FFFFFF'
     * @return void
     */
    public function setCellsBgColor(array $coordinates, string $color): void
    {
        $this->worksheet->getStyle($coordinates)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
    }

    /**
     * @param array $coordinates [int $col, int $row]
     * @param int $size
     * @return void
     */
    public function setCellFontSize(array $coordinates, int $size): void
    {
        $this->worksheet->getStyle(array_merge($coordinates, $coordinates))->getFont()->setSize($size);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @param int $size
     * @return void
     */
    public function setCellsFontSize(array $coordinates, int $size): void
    {
        $this->worksheet->getStyle($coordinates)->getFont()->setSize($size);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @return void
     */
    public function setCellsFontBold(array $coordinates): void
    {
        $this->worksheet->getStyle($coordinates)->getFont()->setBold(true);
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @param string $color ARGB Color
     * @return void
     * @throws AppWorksheetException
     */
    public function setCellsBordersColor(array $coordinates, string $color): void
    {
        $this->getAllBorders($coordinates)->setColor(new Color($color));
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @param string $weight
     * @return void
     * @throws AppWorksheetException
     */
    public function setCellsBordersWeight(array $coordinates, string $weight): void
    {
        $this->getAllBorders($coordinates)->setBorderStyle($weight);
    }

    /**
     * @param string $coordinates e.g. 'A1'
     * @param bool $allowBlank
     * @param string $errorTitle
     * @param string $errorMessage
     * @return void
     * @throws AppWorksheetException
     */
    public function setCellNumberValidation(
        string $coordinates,
        bool $allowBlank,
        string $errorTitle,
        string $errorMessage,
    ) {

        $validation = $this->getDataValidation($coordinates);
        $this->setCellValidation($validation, $allowBlank, $errorTitle, $errorMessage);
        $validation->setType(CellValidation::VALIDATION_CUSTOM);
        $validation->setFormula1(sprintf('=ISNUMBER(%s)', $coordinates));
    }

    /**
     * @param string $coordinates e.g. 'A1'
     * @param bool $allowBlank
     * @param string $errorTitle
     * @param string $errorMessage
     * @param array $options
     * @return void
     * @throws AppWorksheetException
     */
    public function setCellListValidation(
        string $coordinates,
        bool $allowBlank,
        string $errorTitle,
        string $errorMessage,
        array $options
    ) {
        $list = '';
        $countOptions = count($options);
        $index = 1;
        foreach ($options as $option) {
            $comma = ($index < $countOptions) ? ',' : '';
            $list .= $option . $comma;
        }

        $validation = $this->getDataValidation($coordinates);
        $validation->setType(CellValidation::VALIDATION_LIST);
        $validation->setShowDropDown(true);
        $this->setCellValidation($validation, $allowBlank, $errorTitle, $errorMessage);
        $validation->setFormula1('"' . $list . '"');
    }

    /**
     * @param array $coordinates [int $startCol, int $startRow, int $endCol, int $endRow]
     * @return Border
     * @throws AppWorksheetException
     */
    private function getAllBorders(array $coordinates): Border
    {
        try {
            return $this->worksheet->getStyle($coordinates)->getBorders()->getAllBorders();

        } catch (Exception $exception) {
            throw new AppWorksheetException(
                sprintf('Impossible de sélectionner les bordures. %s', $exception->getMessage())
            );
        }
    }

    /**
     * @param string $coordinates
     * @return DataValidation
     * @throws AppWorksheetException
     */
    private function getDataValidation(string $coordinates): DataValidation
    {
        try {
            return $this->worksheet->getCell($coordinates)->getDataValidation();
        } catch (Exception $exception) {
            throw new AppWorksheetException(sprintf(
                'Impossible de sélectionner la validation des cellules. %s',
                $exception->getMessage()
            ));
        }
    }

    private function setCellValidation(
        DataValidation $dataValidation,
        bool $allowBlank,
        string $errorTitle,
        string $errorMessage,
    ) {
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dataValidation->setAllowBlank($allowBlank);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setErrorTitle($errorTitle);
        $dataValidation->setError($errorMessage);
    }
}
