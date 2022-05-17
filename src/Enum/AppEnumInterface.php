<?php

namespace App\Enum;

interface AppEnumInterface
{
    public function getValue(): int;

    public function getLabel(): string;

    public static function getFormChoices(): array;
}
