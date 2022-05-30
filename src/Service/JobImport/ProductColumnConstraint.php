<?php

namespace App\Service\JobImport;

class ProductColumnConstraint extends AbstractColumnConstraint
{
    public function __construct()
    {
        parent::__construct(true);
    }
}
