<?php

namespace App\Service\JobImport;

class DataReport
{
    /**
     * @param int $jobCount
     * @param int $validJobsCount
     * @param int $invalidJobsCount
     * @param InvalidRowReport[]|null $invalidRowsReport
     * @param RefDuplication[]|null $refDuplications
     */
    public function __construct(
        public readonly int $jobCount,
        public readonly int $validJobsCount,
        public readonly int $invalidJobsCount,
        public readonly array|null $invalidRowsReport,
        public readonly array|null $refDuplications,
    ) {}

    /**
     * @return bool
     */
    public function isNotValid(): bool
    {
        return $this->hasInvalidJobs() || $this->hasRefDuplication();
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return !$this->hasInvalidJobs() && !$this->hasRefDuplication();
    }

    /**
     * @return bool
     */
    public function hasInvalidJobs(): bool
    {
        return $this->invalidJobsCount > 0;
    }

    /**
     * @return bool
     */
    public function hasRefDuplication(): bool
    {
        return null !== $this->refDuplications;
    }
}
