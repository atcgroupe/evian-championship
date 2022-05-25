<?php

namespace App\Entity;

use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', message: 'Un compte avec cette adresse mail est déjà enregistré.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Le mail est obligatoire')]
    #[Assert\Email(message: 'Email invalide')]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank(message: 'Le rôle est obligatoire')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le prénom doit faire au minimum 3 caractères',
        maxMessage: 'Le prénom doit faire au maximum 100 caractères'
    )]
    private $firstName;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom doit faire au minimum 3 caractères',
        maxMessage: 'Le nom doit faire au maximum 100 caractères'
    )]
    private $lastName;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserEventNotification::class, orphanRemoval: true)]
    private $eventNotifications;

    #[ORM\Column(type: 'boolean')]
    private $isActive;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire', groups: ['user_create'])]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le mot de passe doit comporter au minimum 8 caractères',
    )]
    private string|null $plainPassword = null;

    public function __construct()
    {
        $this->eventNotifications = new ArrayCollection();
        $this->setDefaults();
    }

    private function setDefaults()
    {
        $this->setIsActive(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDisplayName(string $option): string
    {
        return match ($option) {
            'navbar' => substr($this->getFirstName(), 0, 1),
            'log' => substr($this->getFirstName(), 0, 1) . '. ' . $this->getLastName(),
            'complete' => ucfirst($this->getFirstName() . ' ' . $this->getLastName()),
        };
    }

    /**
     * @return Collection<int, UserEventNotification>
     */
    public function getEventNotifications(): Collection
    {
        return $this->eventNotifications;
    }

    public function addEventNotification(UserEventNotification $eventNotification): self
    {
        if (!$this->eventNotifications->contains($eventNotification)) {
            $this->eventNotifications[] = $eventNotification;
            $eventNotification->setUser($this);
        }

        return $this;
    }

    public function removeEventNotification(UserEventNotification $eventNotification): self
    {
        if ($this->eventNotifications->removeElement($eventNotification)) {
            // set the owning side to null (unless already changed)
            if ($eventNotification->getUser() === $this) {
                $eventNotification->setUser(null);
            }
        }

        return $this;
    }

    public function getEventNotificationStatus(int $value)
    {

    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayRole(): string
    {
        return UserRole::from($this->getRoles()[0])->getLabel();
    }
}
