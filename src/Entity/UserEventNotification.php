<?php

namespace App\Entity;

use App\Enum\JobEvent;
use App\Repository\UserEventNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserEventNotificationRepository::class)]
class UserEventNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint')]
    private $event;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'eventNotifications')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct(User $user, JobEvent $event)
    {
        $this->setUser($user);
        $this->setEvent($event->getValue());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?int
    {
        return $this->event;
    }

    public function setEvent(int $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
