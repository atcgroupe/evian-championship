<?php

namespace App\Service\AppSpreadsheet;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AppSpreadsheet
{
    private Spreadsheet $spreadsheet;

    public function __construct(string $firstSheetTitle)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getActiveSheet()->setTitle($firstSheetTitle);
    }

    public function unset(): void
    {
        unset($this->spreadsheet);
    }

    /**
     * @throws AppWorksheetException
     */
    public function addWorksheet(string $title): void
    {
        try {
            $this->spreadsheet->addSheet(new Worksheet(title: $title));
        } catch (Exception $exception) {
            throw new AppWorksheetException(
                sprintf('Impossible d\'ajouter la feuille %s. %s', $title, $exception->getMessage())
            );
        }
    }

    /**
     * @param string $title
     * @return AppWorksheet|null
     */
    public function getSheetByTitle(string $title): AppWorksheet|null
    {
        $worksheet = $this->spreadsheet->getSheetByName($title);
        return ($worksheet !== null) ? new AppWorksheet($worksheet) : null;
    }

    /**
     * @throws AppWorksheetException
     */
    public function getSheet(int $index): AppWorksheet
    {
        try {
            return new AppWorksheet($this->spreadsheet->getSheet($index));

        } catch (Exception $exception) {
            throw new AppWorksheetException(
                sprintf('La feuille %s est introuvable. %s', $index, $exception->getMessage())
            );
        }
    }

    /**
     * @param string $filename
     * @return void
     * @throws AppWorksheetException
     */
    public function saveAsXlsx(string $filename): void
    {
        $writer = new Xlsx($this->spreadsheet);

        try {
            $writer->save($filename);

        } catch (WriterException $exception) {
            throw new AppWorksheetException(
                sprintf('Impossible d\'enregistrer le fichier. %s', $exception->getMessage())
            );
        }

    }
}
