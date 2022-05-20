<?php

namespace App\Controller\Job;

use App\Enum\JobEvent;
use App\Enum\JobStatus;
use App\Event\AppJobEvent;
use App\Form\JobRejectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job/{id}', name: 'job')]
class JobValidationController extends AbstractJobController
{
    /**
     * Used by CUSTOMER to view job validation files for Approval
     *
     * @param int $id
     * @return Response
     */
    #[Route('/check', name: '_check')]
    public function index(int $id): Response
    {
        $job = $this->jobRepository->findWithRelations($id);

        return $this->render(
            'job/validate.html.twig',
            [
                'job' => $job
            ]
        );
    }

    /**
     * Used by CUSTOMER to Approve a job.
     *
     * @param int $id
     * @return Response
     */
    #[Route('/approve', name: '_approve')]
    public function approve(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_STATUS_FROM_APPROVAL', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour la validation d'un BAT.");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $job->setStatus(JobStatus::APPROVED->getValue());
        $this->manager->flush();

        $this->eventDispatcher->dispatch(
            new AppJobEvent($job, JobEvent::APPROVED, JobStatus::APPROVAL, JobStatus::APPROVED),
            JobEvent::APPROVED->getEvent()
        );

        $this->addFlash(
            'success',
            sprintf(
                'Le job <b class="font-bold">%s</b> a été approuvé. Il va être lancé en production.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }

    /**
     * Used by CUSTOMER to reject a job
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/reject', name: '_reject')]
    public function reject(int $id, Request $request): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_STATUS_FROM_APPROVAL', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour rejeter un BAT.");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $form = $this->createForm(JobRejectType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->setStatus(JobStatus::REJECTED->getValue());
            $this->manager->flush();

            $this->eventDispatcher->dispatch(
                new AppJobEvent($job, JobEvent::REJECTED, JobStatus::APPROVAL, JobStatus::REJECTED),
                JobEvent::REJECTED->getEvent()
            );

            $this->addFlash(
                'warning',
                sprintf(
                    'Le job <b class="font-bold">%s</b> a été refusé. Nous allons regarder l\'erreur au plus vite.',
                    $job->getCustomerReference()
                )
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        return $this->render(
            'job/reject.html.twig',
            [
                'form' => $form->createView(),
                'job' => $job,
            ]
        );
    }

    /**
     * Used by CUSTOMER to ask to modify Job.
     *
     * @param int $id
     * @return Response
     */
    #[Route('/modify', name: '_modify')]
    public function modify(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_STATUS_FROM_APPROVAL', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour rejeter un BAT.");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $this->logManager->addUpdateLog($job, JobStatus::from($job->getStatus()), JobStatus::CREATED);
        $job->setStatus(JobStatus::CREATED->getValue());
        $this->manager->flush();

        $this->eventDispatcher->dispatch(
            new AppJobEvent($job, JobEvent::UPDATE, JobStatus::APPROVAL, JobStatus::CREATED),
            JobEvent::UPDATE->getEvent()
        );

        $this->addFlash(
            'warning',
            sprintf(
                'Le job <b class="font-bold">%s</b> a été repassé en statut Brouillon. Vous pouvez de nouveau le modifier.<br>'
                .'Notre équipe a été avertie de ce changement.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_view', ['id' => $id]);
    }
}
