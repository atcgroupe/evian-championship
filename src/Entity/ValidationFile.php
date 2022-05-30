<?php

namespace App\Entity;

use App\Enum\FileType;
use App\Repository\ValidationFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationFileRepository::class)]
class ValidationFile extends AbstractAppFile
{
    private const PUBLIC_PATH = 'data/validation/';

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'validationFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $job;

    public function __construct(Job $job, string $name, string $sourceName)
    {
        parent::__construct($name, $sourceName);
        $this->setJob($job);
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getType(): FileType
    {
        return FileType::VALIDATION;
    }

    public function getPublicPath(): string
    {
        return self::PUBLIC_PATH . $this->getName();
    }
}
