<?php

namespace App\Service\AppSpreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CellValidation
{
    public const VALIDATION_LIST = DataValidation::TYPE_LIST;
    public const VALIDATION_CUSTOM = DataValidation::TYPE_CUSTOM;
    public const LIST = 'DATA_LIST';
    public const NUMBER = 'DATA_NUMBER';
}
