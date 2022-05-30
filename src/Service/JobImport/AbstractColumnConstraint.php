<?php

namespace App\Service\JobImport;

abstract class AbstractColumnConstraint
{
    public function __construct(
        protected bool $required,
    ) {}

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }
}
