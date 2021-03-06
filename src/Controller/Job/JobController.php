<?php

namespace App\Controller\Job;

use App\Entity\Job;
use App\Entity\JobFile;
use App\Enum\JobEvent;
use App\Enum\FileType;
use App\Event\AppJobEvent;
use App\Form\JobCreateType;
use App\Form\JobUpdateType;
use App\Repository\DeliveryRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job', name: 'job')]
class JobController extends AbstractJobController
{
    /**
     * View a job detail
     *
     * @param int $id
     * @param DeliveryRepository $deliveryRepository
     * @return Response
     */
    #[Route('/{id}/view', name: '_view')]
    public function view(int $id, DeliveryRepository $deliveryRepository): Response
    {
        $job = $this->jobRepository->findWithRelations($id);
        $deliveries = $deliveryRepository->findAll();

        return $this->render(
            'job/view.html.twig',
            [
                'job' => $job,
                'deliveries' => $deliveries,
            ]
        );
    }

    /**
     * Creation of a new job
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(JobCreateType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {
                try {
                    $originalName = $this->fileManager->getOriginalName($file);
                    $safeName = $this->fileManager->save($file, FileType::PRODUCTION);

                    $job->addJobFile(new JobFile($job, $safeName, $originalName));

                } catch (FileException $exception) {
                    $this->addFlash(
                        'danger',
                        "Un probl??me est survenu lors du t??l??chargement du fichier.<br>
                                  veuillez r??essayer s'il vous plait.<br>
                                  Si le probl??me persiste, merci de contacter ATC.<br>
                                  Merci pour votre compr??hension."
                    );
                }
            }

            $this->manager->persist($job);
            $this->manager->flush();

            $this->eventDispatcher->dispatch(new AppJobEvent($job, JobEvent::CREATED), JobEvent::CREATED->getEvent());

            $this->addFlash(
                'success',
                sprintf(
                    'Le job <span class="font-bold">%s</span> ??t?? ajout??!',
                    $job->getCustomerReference()
                )
            );

            return $this->redirectToRoute('job_view', ['id' => $job->getId()]);
        }

        return $this->render(
            'job/edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Update job data
     *
     * Without file upload
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/{id}/update', name: '_update')]
    public function update(int $id, Request $request): Response
    {
        $job = $this->jobRepository->find($id);
        $form = $this->createForm(JobUpdateType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->eventDispatcher->dispatch(
                new AppJobEvent($job, JobEvent::INFO_UPDATED),
                JobEvent::INFO_UPDATED->getEvent()
            );

            $this->addFlash(
                'success',
                sprintf(
                    'Les informations du job <span class="font-bold">%s</span> ont ??t?? modifi??es avec succ??s',
                    $job->getCustomerReference()
                )
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        return $this->render(
            'job/edit.html.twig',
            [
                'form' => $form->createView(),
                'job' => $job,
            ]
        );
    }

    /**
     * Remove a job
     *
     * @param int $id
     * @return Response
     */
    #[Route('/{id}/delete', name: '_delete')]
    public function delete(int $id): Response
    {
        $job = $this->jobRepository->findWithRelations($id);

        if (!$this->isGranted('DELETE', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour supprimer un Job.");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $this->fileManager->removeAll($job->getJobFiles());
        $this->fileManager->removeAll($job->getValidationFiles());
        $this->manager->remove($job);
        $this->manager->flush();

        $this->eventDispatcher->dispatch(new AppJobEvent($job, JobEvent::DELETED), JobEvent::DELETED->getEvent());

        $this->addFlash(
            'success',
            sprintf(
                'Le job <span class="font-bold">%s</span> a ??t?? supprim??.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_index');
    }
}
