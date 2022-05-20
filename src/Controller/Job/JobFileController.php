<?php

namespace App\Controller\Job;

use App\Entity\JobFile;
use App\Enum\FileType;
use App\Enum\JobEvent;
use App\Event\AppJobEvent;
use App\Form\JobFileType;
use App\Repository\JobFileRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job/{id}/job-file', name: 'job_jobfile')]
class JobFileController extends AbstractJobController
{
    /**
     * Upload a job file for a job
     *
     * Just one file can be added.
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/add', name: '_upload')]
    public function addJobFile(int $id, Request $request): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('ADD_JOB_FILE', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour ajouter un fichier de production à un job.");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $form = $this->createForm(JobFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {
                try {
                    $sourceName = $this->fileManager->getOriginalName($file);
                    $name = $this->fileManager->save($file, FileType::PRODUCTION);
                    $job->addJobFile(new JobFile($job, $name, $sourceName));

                } catch (FileException $exception) {
                    $this->addFlash(
                        'danger',
                        "Un problème est survenu lors de l'upload, veuillez réessayer.<br>"
                            . "Si le problème persiste veuillez contacter l'administrateur.");

                    return $this->redirectToRoute('job_view', ['id' => $id]);
                }

                $this->manager->flush();

                $this->eventDispatcher->dispatch(
                    new AppJobEvent($job, JobEvent::JOB_FILE_ADDED),
                    JobEvent::JOB_FILE_ADDED->getEvent()
                );

                $this->addFlash(
                    'success',
                    sprintf(
                        'Le fichier de production <span class="font-bold">%s</span> a été ajouté.',
                        $sourceName
                    )
                );

            }

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        return $this->render(
            'job/edit_job_file.html.twig',
            [
                'form' => $form->createView(),
                'job' => $job,
            ]
        );
    }

    /**
     * Remove a JobFile from a job.
     *
     * @param int $fileId
     * @param JobFileRepository $fileRepository
     * @return RedirectResponse
     */
    #[Route('/{fileId}/remove', name: '_remove')]
    public function removeJobFile(int $fileId, JobFileRepository $fileRepository): RedirectResponse
    {
        $file = $fileRepository->findWithRelations($fileId);
        $job = $file->getJob();

        if (!$this->isGranted('REMOVE_JOB_FILE', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour supprimer un fichier de production.");

            return $this->redirectToRoute('job_view', ['id' => $job->getId()]);
        }

        try {
            $job->removeJobFile($file);
            $this->manager->persist($job);
            $this->fileManager->remove($file);
            $this->manager->flush();

            $this->eventDispatcher->dispatch(
                new AppJobEvent($job, JobEvent::JOB_FILE_REMOVED),
                JobEvent::JOB_FILE_REMOVED->getEvent()
            );

            $this->addFlash(
                'success',
                sprintf(
                    'Le fichier de production <span class="font-bold">%s</span> a été supprimé',
                    $file->getSourceName()
                )
            );

            return $this->redirectToRoute('job_view', ['id' => $job->getId()]);

        } catch (FileException $exception) {
            $this->addFlash('danger', "Erreur: Impossible de supprimer ce fichier.");

            return $this->redirectToRoute('job_view', ['id' => $job->getId()]);
        }
    }
}
