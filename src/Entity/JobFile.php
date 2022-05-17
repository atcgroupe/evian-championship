<?php

namespace App\Entity;

use App\Enum\FileType;
use App\Repository\JobFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobFileRepository::class)]
class JobFile extends AbstractAppFile
{
    private const PUBLIC_PATH = 'data/job/';

    /**
     * @return FileType
     */
    public function getType(): FileType
    {
        return FileType::PRODUCTION;
    }

    /**
     * @return string
     */
    public function getPublicPath(): string
    {
        return self::PUBLIC_PATH . $this->getName();
    }
}
