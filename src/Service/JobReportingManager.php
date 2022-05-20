<?php

namespace App\Service;

use App\Repository\JobRepository;

class JobReportingManager
{
    private float $globalBudget = 0.00;
    private int $totalSurface = 0;
    private array $reportByProduct = [];

    public function __construct(
        private JobRepository $jobRepository,
    ) {
        $this->setData();
    }

    public function getGlobalBudget(): string
    {
        return number_format($this->globalBudget, 2, '.', ' ');
    }

    /**
     * Return surface in m2
     *
     * @return string
     */
    public function getTotalSurface(): string
    {
        return number_format($this->totalSurface / 1000, 0, '.', ' ');
    }

    private function setData()
    {
        $jobs = $this->jobRepository->findAllWithRelations();
        foreach ($jobs as $job)
        {
            $this->globalBudget += $job->getTotalPrice();
            $this->totalSurface +=
                ($job->getWidth() + $job->getLeftBleed() + $job->getRightBleed())
                * ($job->getHeight() + $job->getTopBleed() + $job->getBottomBleed());
        }
    }
}
