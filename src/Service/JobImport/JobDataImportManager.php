<?php

namespace App\Service\JobImport;

use App\Entity\Job;
use App\Enum\JobEvent;
use App\Event\AppJobEvent;
use App\Repository\ProductRepository;
use App\Service\AppSpreadsheet\AppSpreadsheetReader;
use App\Service\AppSpreadsheet\AppWorksheet;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\EventDispatcher\EventDispatcherInterface;

class JobDataImportManager
{
    private ObjectManager $manager;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private AppSpreadsheetReader $spreadsheetReader,
        private ProductRepository $productRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
        $this->manager = $managerRegistry->getManager();
    }

    public function importData()
    {
        $worksheet = $this->spreadsheetReader->getWorksheet(JobImportTemplate::WORKSHEET_NAME);
        $rowIndex = 2;

        if (!$this->hasData($worksheet, $rowIndex)) {
            return null;
        }

        $jobs = [];

        do {
            $job = $this->getJobFromRowData($worksheet, $rowIndex);
            $this->manager->persist($job);
            $jobs[] = $job;

            $rowIndex ++;

        } while ($this->hasData($worksheet, $rowIndex) === true);

        $this->manager->flush();
        $this->dispatchJobsCreationEvent($jobs);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param int $rowIndex
     * @return bool
     */
    private function hasData(AppWorksheet $worksheet, int $rowIndex): bool
    {
        return null !== $worksheet->getCellValue([1, $rowIndex]);
    }

    /**
     * @param AppWorksheet $worksheet
     * @param int $rowIndex
     * @return Job
     */
    private function getJobFromRowData(AppWorksheet $worksheet, int $rowIndex): Job
    {
        $data = $worksheet->getRowCellsValue('A', 'N', $rowIndex);
        $job = new Job();
        $job->setCustomerReference($data[0]);
        $job->setLocation($data[1]);
        $job->setDescription($data[2]);
        $job->setProduct($this->productRepository->findOneBy(['name' => $data[3]]));
        $job->setWidth($data[4]);
        $job->setHeight($data[5]);
        $job->setLeftBleed($data[6]);
        $job->setRightBleed($data[7]);
        $job->setTopBleed($data[8]);
        $job->setBottomBleed($data[9]);
        $job->setFinish($data[10]);
        $job->setImageCount($data[11]);
        $job->setImageQuantity($data[12]);
        $job->setCustomerComment($data[13]);

        return $job;
    }

    /**
     * @param Job[] $jobs
     * @return void
     */
    private function dispatchJobsCreationEvent(array $jobs)
    {
        foreach ($jobs as $job)
        {
            $this->eventDispatcher->dispatch(
                new AppJobEvent($job, JobEvent::CREATED),
                JobEvent::CREATED->getEvent()
            );
        }
    }
}
