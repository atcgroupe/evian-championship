<?php

namespace App\Controller\Job;

use App\Enum\JobStatus;
use App\Service\JobReportingManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobListController extends AbstractJobController
{
    #[Route('/jobs', name: 'job_index')]
    public function index(): Response
    {
        $jobs = $this->jobRepository->findAllWithRelations();

        return $this->render(
            'job/index.html.twig',
            [
                'jobs' => $jobs,
                'jobStatusList' => JobStatus::getFormChoices(),
            ]
        );
    }
}
