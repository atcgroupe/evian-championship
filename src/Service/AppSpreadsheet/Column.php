<?php

namespace App\Service\AppSpreadsheet\AppWorksheet;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Column
{
    public const ALIGN_LEFT = Alignment::HORIZONTAL_LEFT;
    public const ALIGN_CENTER = Alignment::HORIZONTAL_CENTER;
    public const ALIGN_RIGHT = Alignment::HORIZONTAL_RIGHT;
    public const ALIGN_JUSTIFY = Alignment::HORIZONTAL_JUSTIFY;

    public function __construct(
        public readonly string $label,
        public readonly int $width,
        public readonly string $horizontalAlignment = self::ALIGN_LEFT,
        public readonly bool $addTotal = false,
    ) {}
}
