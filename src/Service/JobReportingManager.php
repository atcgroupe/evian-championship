<?php

namespace App\Service;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Service\Xlsx\Column;
use App\Service\Xlsx\XlsxReportHelper;
use PhpOffice\PhpSpreadsheet\Exception;

class JobReportingManager
{
    /**
     * @var Job[] $jobs
     */
    private array $jobs = [];
    private float $globalBudget = 0.00;
    private int $totalSurface = 0;

    public function __construct(
        private JobRepository $jobRepository,
        private XlsxReportHelper $reportHelper,
    ) {
        $this->setData();
    }

    public function getDisplayGlobalBudget(): string
    {
        return number_format($this->globalBudget, 2, '.', ' ');
    }

    /**
     * Return surface in m2
     *
     * @return string
     */
    public function getDisplayTotalSurface(): string
    {
        return number_format($this->totalSurface / 1000, 0, '.', ' ');
    }

    /**
     * @param string $filename
     * @return void
     * @throws Exception
     */
    public function generateXlsxReport(string $filename)
    {
        $this->reportHelper->initializeSpreadsheet();
        $this->reportHelper->addWorksheet('Reporting', $this->getXlsxReportColumns(), $this->getXlsxReportData());
        $this->reportHelper->save($filename);
    }

    /**
     * @return void
     */
    private function setData(): void
    {
        $jobs = $this->jobRepository->findSentWidthRelations();

        if (null === $jobs) {
            return;
        }

        $this->jobs = $jobs;

        foreach ($this->jobs as $job)
        {
            $this->globalBudget += $job->getTotalPrice();
            $this->totalSurface +=
                ($job->getWidth() + $job->getLeftBleed() + $job->getRightBleed())
                * ($job->getHeight() + $job->getTopBleed() + $job->getBottomBleed());

        }
    }

    /**
     * @return Column[]
     */
    private function getXlsxReportColumns(): array
    {
        return [
            new Column('REF', 20),
            new Column('EMPLACEMENT', 40),
            new Column('DESCRIPTIF', 40),
            new Column('PRODUIT', 30),
            new Column('LARGEUR', 15, Column::ALIGN_CENTER),
            new Column('HAUTEUR', 15, Column::ALIGN_CENTER),
            new Column('DÉBORD G.', 15, Column::ALIGN_CENTER),
            new Column('DÉBORD D.', 15, Column::ALIGN_CENTER),
            new Column('DÉBORD H.', 15, Column::ALIGN_CENTER),
            new Column('DÉBORD B.', 15, Column::ALIGN_CENTER),
            new Column('SURFACE (m2)', 15, Column::ALIGN_CENTER, true),
            new Column('MODELES', 15, Column::ALIGN_CENTER),
            new Column('EXEMPLAIRES', 15, Column::ALIGN_CENTER),
            new Column('QTE. TOTALE', 15, Column::ALIGN_CENTER, true),
            new Column('PRIX (€/m2)', 20, Column::ALIGN_CENTER),
            new Column('PRIX UNITAIRE', 20, Column::ALIGN_CENTER),
            new Column('PRIX TOTAL', 20, Column::ALIGN_CENTER, true),
        ];
    }

    /**
     * @return array
     */
    private function getXlsxReportData(): array
    {
        $data = [];
        foreach ($this->jobs as $job) {
            $data[] = [
                $job->getCustomerReference(),
                $job->getLocation(),
                $job->getDescription(),
                $job->getProduct()->getName(),
                $job->getWidth(),
                $job->getHeight(),
                $job->getLeftBleed(),
                $job->getRightBleed(),
                $job->getTopBleed(),
                $job->getBottomBleed(),
                round($job->getTotalBleedSurface(), 2),
                $job->getImageCount(),
                $job->getImageQuantity(),
                $job->getTotalQuantity(),
                $job->getProduct()->getPrice(),
                round($job->getUnitPrice(), 2),
                round($job->getTotalPrice(), 2),
            ];
        }

        return $data;
    }
}
