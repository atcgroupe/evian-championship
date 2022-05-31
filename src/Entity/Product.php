<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity('name', message: 'Cet article est déjà enregistré !')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('api_job_get')]
    private $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire')]
    #[Assert\Length(max: 100, maxMessage: 'La taille maximum pour le nom d\'un produit est de 100 caractères')]
    #[Groups('api_job_get')]
    private $name;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix est obligatoire')]
    #[Assert\Regex('/^[1-9][0-9]{0,2}(\.[0-9]{2})?$/', message: 'Le prix doit être sous la forme 10.00')]
    #[Groups('api_job_get')]
    private $price;

    #[ORM\Column(type: 'boolean')]
    private $isActive;

    public function __construct()
    {
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
