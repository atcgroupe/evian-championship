<?php

namespace App\Service\JobImport;

class RefDuplication
{
    public function __construct(
        public readonly string $name,
        public readonly int $count,
    ) {}
}
