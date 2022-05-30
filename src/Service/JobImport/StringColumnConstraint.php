<?php

namespace App\Service\JobImport;

class StringColumnConstraint extends AbstractColumnConstraint
{
    /**
     * @param bool $required
     * @param int|null $minLength
     * @param int|null $maxLength
     */
    public function __construct(
        bool $required,
        private ?int $minLength = null,
        private ?int $maxLength = null)
    {
        parent::__construct($required);
    }

    /**
     * @return int|null
     */
    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    /**
     * @return bool
     */
    public function hasMinLength(): bool
    {
        return null !== $this->getMinLength();
    }

    /**
     * @param int|null $minLength
     */
    public function setMinLength(?int $minLength): void
    {
        $this->minLength = $minLength;
    }

    /**
     * @return int|null
     */
    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * @return bool
     */
    public function hasMaxLength(): bool
    {
        return null !== $this->getMaxLength();
    }
}
