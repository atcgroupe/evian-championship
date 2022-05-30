<?php

namespace App\Service\JobImport;

use App\Entity\Job;
use App\Entity\Product;
use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use App\Service\AppSpreadsheet\Column;

class ConstraintDataChecker
{
    public function __construct(
        private ProductRepository $productRepository,
        private JobRepository $jobRepository,
    ) {}

    /**
     * @param Column $column
     * @param AbstractColumnConstraint $constraint
     * @param int|string|null $value
     * @return true|string returns true if the value is valid, or error Message.
     */
    public function check(Column $column, AbstractColumnConstraint $constraint, int|string|null $value): bool|string
    {
        if ($constraint->isRequired() && null === $value) {
            return sprintf('Le champ %s est requis', $column->label);
        }

        if (!$constraint->isRequired() && null === $value) {
            return true;
        }

        return match ($constraint::class) {
            StringColumnConstraint::class => $this->isStringValid($value, $column, $constraint),
            IntColumnConstraint::class => $this->isIntValid($value, $column),
            ProductColumnConstraint::class => $this->isProductValid($value),
            JobRefColumnConstraint::class => $this->isJobRefValid($value, $column, $constraint),
            default => false,
        };
    }

    /**
     * @param Column $column
     * @param mixed $value
     * @param StringColumnConstraint $constraint
     * @return true|string
     */
    private function isStringValid(mixed $value, Column $column, StringColumnConstraint $constraint): bool|string
    {
        $value = strval($value);

        $length = strlen($value);
        if ($constraint->hasMinLength() && $length < $constraint->getMinLength()) {
            return sprintf(
                'Le champ %s doit faire %s caractères minimum',
                $column->label,
                $constraint->getMinLength()
            );
        }

        if ($constraint->hasMaxLength() && $length > $constraint->getMaxLength()) {
            return sprintf(
                'Le champ %s doit faire %s caractères maximum',
                $column->label,
                $constraint->getMaxLength()
            );
        }

        return true;
    }

    /**
     * @param mixed $value
     * @param Column $column
     * @return true|string
     */
    private function isIntValid(mixed $value, Column $column): bool|string
    {
        if (!is_int($value)) {
            return sprintf('Le champ %s n\'est pas un nombre valide', $column->label);
        }

        return true;
    }

    /**
     * @param mixed $value
     * @return true|string
     */
    private function isProductValid(mixed $value): bool|string
    {
        $value = strval($value);

        $product = $this->productRepository->findOneBy(['name' => $value]);
        if (!$product instanceof Product) {
            return sprintf(
                'Le produit %s n\'existe pas. Il faut bien sélectionner un produit dans la liste et ne pas le modifier',
                $value
            );
        }

        return true;
    }

    /**
     * @param mixed $value
     * @param Column $column
     * @param JobRefColumnConstraint $constraint
     * @return bool|string
     */
    private function isJobRefValid(mixed $value, Column $column, JobRefColumnConstraint $constraint): bool|string
    {
        $value = strval($value);

        $job = $this->jobRepository->findOneBy(['customerReference' => $value]);
        if ($job instanceof Job) {
            return sprintf('Un job avec la référence %s existe déjà dans l\'application.', $value);
        }

        return $this->isStringValid($value, $column, $constraint);
    }
}
