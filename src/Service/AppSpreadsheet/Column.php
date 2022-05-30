<?php

namespace App\Service\AppSpreadsheet;

class Column
{
    public function __construct(
        public readonly string $label,
        public readonly int $width,
        public readonly string $horizontalAlignment = CellAlignment::HORIZONTAL_LEFT,
        public readonly bool $addTotal = false,
        public readonly string|null $dataValidation = null,
        public readonly array|null $dataValidationList = null,
    ) {}
}
