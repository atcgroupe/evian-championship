<?php

namespace App\Entity;

use App\Enum\FileType;
use App\Repository\JobFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobFileRepository::class)]
class JobFile extends AbstractAppFile
{
    private const PUBLIC_PATH = 'data/job/';

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'jobFiles')]
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
