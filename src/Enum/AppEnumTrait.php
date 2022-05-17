<?php

namespace App\Enum;

trait AppEnumTrait
{
    public function getValue(): int
    {
        return $this->value;
    }

    public static function getFormChoices(): array
    {
        $list = [];
        foreach (self::cases() as $case) {
            $list[$case->getLabel()] = $case->getValue();
        }

        return $list;
    }
}
