<?php

namespace App\Entity;

use App\Enum\FileType;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping as ORM;

#[MappedSuperclass]
abstract class AbstractAppFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 100)]
    private $sourceName;

    #[ORM\Column(type: 'datetime')]
    private $date;

    public function __construct(string $name, string $sourceName)
    {
        $this->setName($name);
        $this->setSourceName($sourceName);

        $this->setDefaults();
    }

    private function setDefaults()
    {
        $this->date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
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

    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }

    public function setSourceName(string $sourceName): self
    {
        $this->sourceName = $sourceName;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    abstract public function getType(): FileType;

    /**
     * Must return the public file path for this file type.
     *
     * @return string
     */
    abstract public function getPublicPath(): string;
}
