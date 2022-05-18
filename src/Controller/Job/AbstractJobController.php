<?php

namespace App\Controller\Job;

use App\Repository\JobRepository;
use App\Service\AppFileManager;
use App\Service\JobLogManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractJobController extends AbstractController
{
    protected ObjectManager $manager;

    public function __construct(
        protected JobRepository $jobRepository,
        private ManagerRegistry $registry,
        protected AppFileManager $fileManager,
        protected JobLogManager $logManager,
    ) {
        $this->manager = $this->registry->getManager();
    }
}
