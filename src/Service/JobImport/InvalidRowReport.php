<?php

namespace App\Service\JobImport;

class InvalidRowReport
{
    /**
     * @param int $rowIndex
     * @param string[]|null $constraintViolations
     */
    public function __construct(
        public readonly int $rowIndex,
        public readonly ?array $constraintViolations = null,
    ) {}
}
