<?php

namespace App\Controller\Job;

use App\Enum\JobStatus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job/{id}/status-request', name: 'job_status_request')]
class JobStatusRequestController extends AbstractJobController
{
    #[Route('/to-canceled', name: '_update_to_canceled')]
    public function requestUpdateStatusToCanceled(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('REQUEST_UPDATE_STATUS_TO_CANCELED', $job)) {
            $this->addFlash('danger',
                "Vous n'avez pas les droits pour demander l'annulation ce job"
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $job->setStatus(JobStatus::STANDBY->getValue());
        $job->setStandbyComment('Le client a demandé l\'annulation de ce job');
        $this->manager->flush();

        // Todo: send notification to COMPANY_USER

        $this->addFlash(
            'success',
            sprintf(
                'La demande d\'annulation du job <b class="font-bold">%s</b> a été demandée.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }

    /**
     * Used to send job Back to CUSTOMER for update.
     *
     * @param int $id
     * @return Response
     */
    #[Route('/to-created', name: '_update_to_created')]
    public function requestUpdateStatusToCreated(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('REQUEST_UPDATE_STATUS_TO_CREATED', $job)) {
            $this->addFlash('danger',
                "Vous n'avez pas les droits de demander la modification de ce job"
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $job->setStatus(JobStatus::STANDBY->getValue());
        $job->setStandbyComment('Le client a demandé à modifier ce job');
        $this->manager->flush();

        // Todo: Send notification to COMPANY_USER

        $this->addFlash(
            'success',
            sprintf(
                'La demande de modification du job <b class="font-bold">%s</b> a été envoyée.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }
}
