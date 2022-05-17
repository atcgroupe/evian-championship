<?php

namespace App\Entity;

use App\Enum\FileType;
use App\Repository\ValidationFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationFileRepository::class)]
class ValidationFile extends AbstractAppFile
{
    private const PUBLIC_PATH = 'data/job/';

    public function getType(): FileType
    {
        return FileType::VALIDATION;
    }

    public function getPublicPath(): string
    {
        return self::PUBLIC_PATH . $this->getName();
    }
}
