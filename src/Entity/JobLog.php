<?php

namespace App\Entity;

use App\Repository\JobLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobLogRepository::class)]
class JobLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $action;

    #[ORM\Column(type: 'datetime')]
    private $datetime;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'jobLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private $job;

    #[ORM\Column(type: 'string', length: 100)]
    private $user;

    public function __construct(Job $job, string $user, string $action)
    {
        $this->setJob($job);
        $this->setUser($user);
        $this->setAction($action);
        $this->setDefaults();
    }

    private function setDefaults()
    {
        $this->datetime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
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

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }
}
