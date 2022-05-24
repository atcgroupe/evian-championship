<?php

namespace App\Service\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Exception;

class XlsxReportFormatter
{
    /**
     * @param Worksheet $worksheet
     * @param Column[] $columns
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function formatWorksheet(Worksheet $worksheet, array $columns, array $data)
    {
        $cols = count($columns);
        $rows = count($data);
        $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->setVerticalAlignment($worksheet, $cols, $rows);
        $this->setHorizontalAlignment($worksheet, $columns, $cols, $rows);
        $this->setHeaderContent(
            $worksheet,
            sprintf('Reporting ATC Groupe | Evian Championship - (%s)', $date->format('d/m/Y'))
        );
        $this->setHeaderStyle($worksheet, $cols);
        $this->setColumnsHeadingContent($worksheet, $columns);
        $this->setColumnsHeadingStyle($worksheet, $cols);
        $this->setData($worksheet, $data);
        $this->setDataStyle($worksheet, $data);
        $this->setTotalContent($worksheet, $columns, $rows);
        $this->setTotalStyle($worksheet, $cols, $rows);
        $this->setColumnsWidth($worksheet, $columns);
    }

    private function setVerticalAlignment(Worksheet $worksheet, int $cols, int $rows)
    {
        $worksheet->getStyle([1, 1, $cols, $rows + 3])->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    /**
     * @param Worksheet $worksheet
     * @param Column[] $columns
     * @param int $cols
     * @param int $rows
     * @return void
     */
    private function setHorizontalAlignment(Worksheet $worksheet, array $columns, int $cols, int $rows)
    {
        $worksheet->getStyle([1, 1, $cols, 2])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $col = 1;
        foreach ($columns as $column) {
            $worksheet->getStyle([$col, 3, $col, $rows + 3])->getAlignment()->setHorizontal($column->alignment);
            $col ++;
        }
    }

    /**
     * @param Worksheet $worksheet
     * @param string $title
     * @return void
     */
    private function setHeaderContent(Worksheet $worksheet, string $title)
    {
        $worksheet->setCellValue('A1', $title);
    }

    /**
     * @param Worksheet $worksheet
     * @param int $cols Number of columns
     * @return void
     * @throws Exception
     */
    private function setHeaderStyle(Worksheet $worksheet, int $cols)
    {
        $worksheet->mergeCells([1,1,$cols,1]);
        $worksheet->getStyle('A1')->getFont()->getColor()->setRGB('B40F69');
        $worksheet->getStyle('A1')->getFont()->setSize(20);
        $worksheet->getRowDimension('1')->setRowHeight(50);
    }

    /**
     * @param Worksheet $worksheet
     * @param Column[] $columns
     * @return void
     */
    public function setColumnsHeadingContent(Worksheet $worksheet, array $columns)
    {
        $data = [];
        foreach ($columns as $column) {
            $data[] = $column->label;
        }

        $worksheet->fromArray($data, startCell: 'A2');
    }

    /**
     * @param Worksheet $worksheet
     * @param int $cols
     * @return void
     * @throws Exception
     */
    private function setColumnsHeadingStyle(Worksheet $worksheet, int $cols)
    {
        $style = $worksheet->getStyle([1,2, $cols, 2]);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('B40F69');
        $style->getFont()->getColor()->setRGB('FFFFFF');
        $style->getFont()->setSize(14);
        $style->getBorders()->getAllBorders()->setColor(new Color(Color::COLOR_WHITE));
        $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $worksheet->getRowDimension('2')->setRowHeight(40);
    }

    /**
     * @param Worksheet $worksheet
     * @param array $data
     * @return void
     */
    private function setData(Worksheet $worksheet, array $data)
    {
        $worksheet->fromArray($data, startCell: 'A3');
    }

    /**
     * @param Worksheet $worksheet
     * @param array $data
     * @return void
     */
    private function setDataStyle(Worksheet $worksheet, array $data): void
    {
        for ($i = 3; $i <= count($data) + 2; $i ++) {
            $worksheet->getRowDimension($i)->setRowHeight(30);
        }
    }

    /**
     * @param Worksheet $worksheet
     * @param int $rows
     * @param Column[] $columns
     * @return void
     */
    private function setTotalContent(Worksheet $worksheet, array $columns, int $rows)
    {
        $rowIndex = $rows + 3;
        $colIndex = 'A';
        foreach ($columns as $column) {
            if ($colIndex === 'A') {
                $worksheet->setCellValue([1, $rowIndex], 'TOTAL');
            }

            if ($colIndex !== 'A' && $column->addTotal) {
                $worksheet->setCellValue($colIndex . $rowIndex, sprintf('=SUM(%s3:%s%s)', $colIndex, $colIndex, $rows + 2));
            }

            $colIndex ++;
        }
    }

    /**
     * @param Worksheet $worksheet
     * @param int $cols
     * @param int $rows
     * @return void
     */
    private function setTotalStyle(Worksheet $worksheet, int $cols, int $rows)
    {
        $rowIndex = $rows + 3;
        $style = $worksheet->getStyle([1,$rowIndex, $cols, $rowIndex]);
        $style->getFont()->getColor()->setRGB('B40F69');
        $style->getFont()->setBold(true);
        $worksheet->getRowDimension($rowIndex)->setRowHeight(40);
    }

    /**
     * @param Worksheet $worksheet
     * @param Column[] $columns
     * @return void
     */
    private function setColumnsWidth(Worksheet $worksheet, array $columns): void
    {
        $columnIndex = 'A';
        foreach ($columns as $column) {
            $worksheet->getColumnDimension($columnIndex)->setWidth($column->width);
            $columnIndex ++;
        }
    }
}
