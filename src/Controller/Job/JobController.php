<?php

namespace App\Controller\Job;

use App\Entity\Job;
use App\Entity\JobFile;
use App\Enum\FileType;
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
                        "Un problème est survenu lors du téléchargement du fichier.<br>
                                  veuillez réessayer s'il vous plait.<br>
                                  Si le problème persiste, merci de contacter ATC.<br>
                                  Merci pour votre compréhension."
                    );
                }
            }

            $this->manager->persist($job);
            $this->logManager->addLog($job, 'Création du job');
            $this->manager->flush();
            $this->addFlash(
                'success',
                sprintf(
                    'Le job <span class="font-bold">%s</span> été ajouté!',
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
            $this->logManager->addLog($job, 'Modification des informations.');
            $this->manager->flush();
            $this->addFlash(
                'success',
                sprintf(
                    'Les informations du job <span class="font-bold">%s</span> ont été modifiées avec succès',
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
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('DELETE', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour supprimer un Job.");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $this->manager->remove($job);
        $this->manager->flush();
        $this->addFlash(
            'success',
            sprintf(
                'Le job <span class="font-bold">%s</span> a été supprimé.',
                $job->getCustomerReference()
            )
        );

        return $this->redirectToRoute('job_index');
    }
}
