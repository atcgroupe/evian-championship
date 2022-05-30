<?php

namespace App\Service\JobImport;

use App\Repository\ProductRepository;
use App\Service\AppSpreadsheet\CellAlignment;
use App\Service\AppSpreadsheet\CellValidation;
use App\Service\AppSpreadsheet\Column;

class TemplateColumnsBuilder
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {}

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return [
            1 => new Column('REF', 20, CellAlignment::HORIZONTAL_CENTER),
            2 => new Column('EMPLACEMENT', 40),
            3 => new Column('DESCRIPTION', 40),
            4 => new Column('PRODUIT', 30, dataValidation: CellValidation::LIST, dataValidationList: $this->getProductLabels()),
            5 => new Column('LARGEUR (mm)', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            6 => new Column('HAUTEUR (mm)', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            7 => new Column('DEBORD G (mm)', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            8 => new Column('DEBORD D (mm)', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            9 => new Column('DEBORD H (mm)', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            10 => new Column('DEBORD B (mm)', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            11 => new Column('FINITION', 40),
            12 => new Column('NB VISUELS', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            13 => new Column('QTE. PAR VISUEL', 20, CellAlignment::HORIZONTAL_CENTER, dataValidation: CellValidation::NUMBER),
            14 => new Column('COMMENTAIRE', 80),
        ];
    }

    /**
     * @return AbstractColumnConstraint[]
     */
    public function getColumnConstraints(): array
    {
        return [
            1 => new JobRefColumnConstraint(true, maxLength: 100),
            2 => new StringColumnConstraint(false, maxLength: 100),
            3 => new StringColumnConstraint(false, maxLength: 255),
            4 => new ProductColumnConstraint(),
            5 => new IntColumnConstraint(true),
            6 => new IntColumnConstraint(true),
            7 => new IntColumnConstraint(false),
            8 => new IntColumnConstraint(false),
            9 => new IntColumnConstraint(false),
            10 => new IntColumnConstraint(false),
            11 => new StringColumnConstraint(false, maxLength: 255),
            12 => new IntColumnConstraint(true),
            13 => new IntColumnConstraint(true),
            14 => new StringColumnConstraint(false)
        ];
    }

    /**
     * @return string[]
     */
    private function getProductLabels(): array
    {
        $products = $this->productRepository->findBy(['isActive' => true]);
        $productsLabels = [];
        foreach ($products as $product) {
            $productsLabels[] = $product->getName();
        }

        return $productsLabels;
    }
}
