<?php

namespace App\Entity;

use App\Enum\JobStatus;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[UniqueEntity('customerReference', message: 'Il existe déjà un job qui porte ce nom !')]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'La référence n\'est pas définie')]
    #[Assert\Length(max: 100, maxMessage: 'La référence du job peut comporter au maximum 100 caractères.')]
    private $customerReference;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Assert\Length(max: 100, maxMessage: 'La localisation peut comporter au maximum 100 caractères.')]
    private $location;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'La description du job peut comporter au maximum 255 caractères.')]
    private $description;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'La largeur du Job n\'est pas définie')]
    private $width;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'La hauteur du Job n\'est pas définie')]
    private $height;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero(message: 'Le débord gauche doit être un entier positif')]
    private $leftBleed;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero(message: 'Le débord droit doit être un entier positif')]
    private $rightBleed;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero(message: 'Le débord haut doit être un entier positif')]
    private $topBleed;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero(message: 'Le débord bas doit être un entier positif')]
    private $bottomBleed;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'La finition du job peut comporter au maximum 255 caractères.')]
    private $finish;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Le nombre de visuels n\'est pas définie')]
    #[Assert\Positive(message: 'Ce nombre ne peut être qu\'un un entier positif')]
    private $imageCount;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'La quantité pour chaque modèle n\'est pas définie')]
    #[Assert\Positive(message: 'Ce nombre ne peut être qu\'un entier positif')]
    private $imageQuantity;

    #[ORM\Column(type: 'text', nullable: true)]
    private $customerComment;

    #[ORM\Column(type: 'text', nullable: true)]
    private $standbyComment;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(
        message: 'Merci de nous donner la raison de ce rejet.',
        groups: ['reject']
    )]
    private $rejectComment;

    #[ORM\Column(type: 'smallint')]
    private $status;

    #[ORM\ManyToOne(targetEntity: Delivery::class, inversedBy: 'jobs')]
    private $delivery;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: JobFile::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $jobFiles;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: ValidationFile::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $validationFiles;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: JobLog::class,  cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
    private $jobLogs;

    public function __construct()
    {
        $this->setDefaults();
        $this->jobFiles = new ArrayCollection();
        $this->validationFiles = new ArrayCollection();
        $this->jobLogs = new ArrayCollection();
    }

    public function setDefaults()
    {
        $this->setLeftBleed(0);
        $this->setRightBleed(0);
        $this->setTopBleed(0);
        $this->setBottomBleed(0);
        $this->setStatus(JobStatus::CREATED->getValue());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerReference(): ?string
    {
        return $this->customerReference;
    }

    public function setCustomerReference(string $customerReference): self
    {
        $this->customerReference = $customerReference;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getLeftBleed(): ?int
    {
        return $this->leftBleed;
    }

    public function setLeftBleed(int $leftBleed): self
    {
        $this->leftBleed = $leftBleed;

        return $this;
    }

    public function getRightBleed(): ?int
    {
        return $this->rightBleed;
    }

    public function setRightBleed(int $rightBleed): self
    {
        $this->rightBleed = $rightBleed;

        return $this;
    }

    public function getTopBleed(): ?int
    {
        return $this->topBleed;
    }

    public function setTopBleed(int $topBleed): self
    {
        $this->topBleed = $topBleed;

        return $this;
    }

    public function getBottomBleed(): ?int
    {
        return $this->bottomBleed;
    }

    public function setBottomBleed(int $bottomBleed): self
    {
        $this->bottomBleed = $bottomBleed;

        return $this;
    }

    public function getFinish(): ?string
    {
        return $this->finish;
    }

    public function setFinish(?string $finish): self
    {
        $this->finish = $finish;

        return $this;
    }

    public function getImageCount(): ?int
    {
        return $this->imageCount;
    }

    public function setImageCount(int $imageCount): self
    {
        $this->imageCount = $imageCount;

        return $this;
    }

    public function getImageQuantity(): ?int
    {
        return $this->imageQuantity;
    }

    public function setImageQuantity(int $imageQuantity): self
    {
        $this->imageQuantity = $imageQuantity;

        return $this;
    }

    public function getCustomerComment(): ?string
    {
        return $this->customerComment;
    }

    public function setCustomerComment(?string $customerComment): self
    {
        $this->customerComment = $customerComment;

        return $this;
    }

    public function getStandbyComment(): ?string
    {
        return $this->standbyComment;
    }

    public function setStandbyComment(?string $standbyComment): self
    {
        $this->standbyComment = $standbyComment;

        return $this;
    }

    public function getRejectComment(): ?string
    {
        return $this->rejectComment;
    }

    public function setRejectComment(?string $rejectComment): self
    {
        $this->rejectComment = $rejectComment;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, JobFile>
     */
    public function getJobFiles(): Collection
    {
        return $this->jobFiles;
    }

    public function addJobFile(JobFile $jobFile): self
    {
        if (!$this->jobFiles->contains($jobFile)) {
            $this->jobFiles[] = $jobFile;
            $jobFile->setJob($this);
        }

        return $this;
    }

    public function removeJobFile(JobFile $jobFile): self
    {
        if ($this->jobFiles->removeElement($jobFile)) {
            // set the owning side to null (unless already changed)
            if ($jobFile->getJob() === $this) {
                $jobFile->setJob(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ValidationFile>
     */
    public function getValidationFiles(): Collection
    {
        return $this->validationFiles;
    }

    public function addValidationFile(ValidationFile $validationFile): self
    {
        if (!$this->validationFiles->contains($validationFile)) {
            $this->validationFiles[] = $validationFile;
            $validationFile->setJob($this);
        }

        return $this;
    }

    public function removeValidationFile(ValidationFile $validationFile): self
    {
        if ($this->validationFiles->removeElement($validationFile)) {
            // set the owning side to null (unless already changed)
            if ($validationFile->getJob() === $this) {
                $validationFile->setJob(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JobLog>
     */
    public function getJobLogs(): Collection
    {
        return $this->jobLogs;
    }

    public function addJobLog(JobLog $jobLog): self
    {
        if (!$this->jobLogs->contains($jobLog)) {
            $this->jobLogs[] = $jobLog;
            $jobLog->setJob($this);
        }

        return $this;
    }

    public function removeJobLog(JobLog $jobLog): self
    {
        if ($this->jobLogs->removeElement($jobLog)) {
            // set the owning side to null (unless already changed)
            if ($jobLog->getJob() === $this) {
                $jobLog->setJob(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayQuantity(): string
    {
        if ($this->getImageCount() == 1) {
            return sprintf('%sEX', $this->getImageQuantity());
        }

        return sprintf('%s MOD. en %sEX', $this->getImageCount(), $this->getImageQuantity());
    }

    /**
     * @return int
     */
    public function getTotalQuantity(): int
    {
        return $this->getImageQuantity() * $this->getImageCount();
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return (($this->getWidth()/1000) * ($this->getHeight()/1000)) * $this->getProduct()->getPrice();
    }

    /**
     * @return string
     */
    public function getDisplayUnitPrice(): string
    {
        return number_format(round($this->getUnitPrice(), 2), 2, '.', ' ');
    }

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->getUnitPrice() * $this->getImageCount() * $this->getImageQuantity();
    }

    /**
     * @return string
     */
    public function getDisplayTotalPrice(): string
    {
        return number_format(round($this->getTotalPrice(), 2), 2, '.', ' ');
    }

    /**
     * @return string
     */
    public function getDisplayStatus(): string
    {
        return JobStatus::from($this->getStatus())->getLabel();
    }

    /**
     * @return JobStatus
     */
    public function getJobStatus(): JobStatus
    {
        return JobStatus::from($this->getStatus());
    }

    /**
     * Surface in m2
     *
     * @return float
     */
    public function getUnitVisibleSurface(): float
    {
        return ($this->getWidth() * $this->getHeight()) / 1000;
    }

    /**
     * @return float
     */
    public function getUnitBleedSurface(): float
    {
        return (
            ($this->getWidth() + $this->getLeftBleed() + $this->getRightBleed())
            * ($this->getHeight() + $this->getTopBleed() + $this->getBottomBleed())
        ) / 1000;
    }

    /**
     * @return float
     */
    public function getTotalVisibleSurface(): float
    {
        return $this->getUnitVisibleSurface() * $this->getTotalQuantity();
    }

    /**
     * @return float
     */
    public function getTotalBleedSurface(): float
    {
        return $this->getUnitBleedSurface() * $this->getTotalQuantity();
    }
}
