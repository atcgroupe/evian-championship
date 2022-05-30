<?php

namespace App\Controller\Job;

use App\Enum\JobStatus;
use App\Service\JobReportingManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jobs/reporting', name: 'job_reporting')]
class JobReportingController extends AbstractJobController
{
    #[Route('', name: '_index')]
    public function index(JobReportingManager $reportManager): Response
    {
        $jobs = $this->jobRepository->findSentWidthRelations();

        return $this->render(
            'job/reporting.html.twig',
            [
                'jobs' => $jobs,
                'jobStatusList' => JobStatus::getFormChoices(),
                'globalBudget' => $reportManager->getDisplayGlobalBudget(),
                'totalSurface' => $reportManager->getDisplayTotalSurface()
            ]
        );
    }

    #[Route('/download', name: '_download')]
    public function download(JobReportingManager $reportingManager): BinaryFileResponse
    {
        $temp = tempnam(sys_get_temp_dir(), 'xlsx_report_');
        $file = $temp . '.xlsx';
        rename($temp, $file);
        $reportingManager->generateXlsxReport($file);

        $response = new BinaryFileResponse(
            $file,
            headers: ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf(
                'ATC Groupe Reporting %s.xlsx',
                (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('d-m-Y')
            )
        );
        return $response;
    }
}
