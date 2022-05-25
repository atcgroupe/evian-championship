<?php

namespace App\Enum;

interface AppEnumInterface
{
    public function getValue(): int|string;

    public function getLabel(): string;

    public static function getFormChoices(): array;
}
