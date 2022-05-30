<?php

namespace App\Service\JobImport;

class JobRefColumnConstraint extends StringColumnConstraint
{
    public function __construct(bool $required, ?int $minLength = null, ?int $maxLength = null)
    {
        parent::__construct($required, $minLength, $maxLength);
    }
}
