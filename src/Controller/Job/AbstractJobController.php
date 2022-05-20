<?php

namespace App\Controller\Job;

use App\Repository\JobRepository;
use App\Service\AppFileManager;
use App\Service\JobLogManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractJobController extends AbstractController
{
    protected ObjectManager $manager;

    public function __construct(
        protected JobRepository $jobRepository,
        private ManagerRegistry $registry,
        protected AppFileManager $fileManager,
        protected JobLogManager $logManager,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
        $this->manager = $this->registry->getManager();
    }
}
