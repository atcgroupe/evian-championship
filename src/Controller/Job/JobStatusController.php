<?php

namespace App\Controller\Job;

use App\Enum\JobStatus;
use App\Form\JobStatusType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job/{id}/status', name: 'job_status')]
class JobStatusController extends AbstractJobController
{
    /**
     * Update job status
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/update', name: '_update')]
    public function updateStatus(int $id, Request $request): Response
    {
        $job = $this->jobRepository->find($id);
        $preStatus = JobStatus::from($job->getStatus());

        if (!$this->isGranted('UPDATE_STATUS', $job)) {
            $this->addFlash("danger", "Vous n'avez pas les droits pour changer le statut d'un job");

            $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $form = $this->createForm(JobStatusType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logManager->addUpdateLog($job, $preStatus, JobStatus::from($job->getStatus()));
            $this->manager->flush();
            $this->addFlash(
                'success',
                sprintf(
                    'Le statut du job <b class="font-bold">%s</b> a été modifié.',
                    $job->getCustomerReference()
                )
            );

            // Todo: Add customer e-mail notification

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        return $this->render(
            'job/edit_status.html.twig',
            [
                'form' => $form->createView(),
                'job' => $job,
            ]
        );
    }

    /**
     * Used to change job status from CREATED to SENT
     *
     * Used by customer
     *
     * @param int $id
     * @return Response
     */
    #[Route('/to-sent', name: '_update_to_sent')]
    public function updateFromCreatedToSent(int $id): Response
    {
        $job = $this->jobRepository->find($id);
        if (!$this->isGranted('UPDATE_STATUS_FROM_CREATED_TO_SENT', $job)) {
            $this->addFlash('danger',
                "Ce job ne peut pas être envoyé à ATC pour l'une des raisons suivantes:<br>
                - Vous n'avez pas les droits<br>
                - Le job n'est pas en statut brouillon<br>
                - Aucun fichier de production n'a été attaché a ce job"
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $job->setStatus(JobStatus::SENT->getValue());
        $this->logManager->addUpdateLog($job, JobStatus::CREATED, JobStatus::SENT);
        $this->manager->flush();

        // Todo: Add notification to GRAPHIC_DESIGNER

        $this->addFlash(
            'success',
            sprintf(
                'Le job <b class="font-bold">%s</b> a été envoyé à ATC.<br>'
                    .'Vous serez notifié sous peu lorsque le BAT sera disponible pour validation.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }

    /**
     * Used to send a job to CUSTOMER for APPROVAL.
     *
     * @param int $id
     * @return Response
     */
    #[Route('/to-approval', name: '_update_to_approval')]
    public function updateStatusToApproval(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_STATUS_FROM_SENT_TO_APPROVAL', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour la validation d'un BAT.");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $this->logManager->addUpdateLog($job, JobStatus::from($job->getStatus()), JobStatus::APPROVAL);
        $job->setStatus(JobStatus::APPROVAL->getValue());
        $job->setRejectComment(null);
        $this->manager->flush();

        $this->addFlash(
            'success',
            sprintf(
                'Le job <b class="font-bold">%s</b> a été envoyé pour validation.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }

    /**
     * Used to change job status from APPROVED to PRODUCTION
     *
     * Used by COMPANY_USER
     *
     * @param int $id
     * @return Response
     */
    #[Route('/to-production', name: '_update_to_production')]
    public function updateFromApprovedToProduction(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_STATUS_FROM_APPROVED_TO_PRODUCTION', $job)) {
            $this->addFlash('danger',
                "Vous n'avez pas les droits pour envoyer ce job en production"
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $job->setStatus(JobStatus::PRODUCTION->getValue());
        $this->logManager->addUpdateLog($job, JobStatus::APPROVED, JobStatus::PRODUCTION);
        $this->manager->flush();
        $this->addFlash(
            'success',
            sprintf(
                'Le job <b class="font-bold">%s</b> a été envoyé en production.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }

    /**
     * Used to cancel a job
     *
     * @param int $id
     * @return Response
     */
    #[Route('/to-canceled', name: '_update_to_canceled')]
    public function cancel(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_STATUS_TO_CANCELED', $job)) {
            $this->addFlash('danger',
                "Vous n'avez pas les droits pour annuler ce job"
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $this->logManager->addUpdateLog($job, JobStatus::from($job->getStatus()), JobStatus::CANCELED);
        $job->setStatus(JobStatus::CANCELED->getValue());
        $this->manager->flush();

        // Todo: Add notification to CUSTOMER

        $this->addFlash(
            'success',
            sprintf(
                'Le job <b class="font-bold">%s</b> a été annulé.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }

    /**
     * Used to send job back to customer
     *
     * @param int $id
     * @return Response
     */
    #[Route('/to-created', name: '_update_to_created')]
    public function sendToCustomer(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_STATUS_TO_CREATED', $job)) {
            $this->addFlash('danger',
                "Vous n'avez pas les droits de renvoyer ce job au client pour modification"
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $this->logManager->addUpdateLog($job, JobStatus::from($job->getStatus()), JobStatus::CREATED);
        $job->setStatus(JobStatus::CREATED->getValue());
        $job->setStandbyComment(null);
        $this->manager->flush();

        // Todo: Add notification to CUSTOMER

        $this->addFlash(
            'success',
            sprintf(
                'Le job <b class="font-bold">%s</b> a été renvoyé au client pour modification.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }
}