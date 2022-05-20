<?php

namespace App\Controller\Job;

use App\Entity\ValidationFile;
use App\Enum\FileType;
use App\Enum\JobEvent;
use App\Event\AppJobEvent;
use App\Form\JobValidationFileType;
use App\Repository\JobFileRepository;
use App\Repository\ValidationFileRepository;
use App\Service\AppFileManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

#[Route('/job/{id}/validation-file', name: 'job_validation_file')]
class JobValidationFileController extends AbstractJobController
{
    #[Route('/add', name: '_add')]
    public function addValidationFile(int $id, Request $request): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('ADD_VALIDATION_FILE', $job)) {
            $this->addFlash('danger', "Impossible d'ajouter un BAT");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $form = $this->createForm(JobValidationFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('file')->getData();

            if (!empty($files)) {
                foreach ($files as $file) {
                    try {
                        $sourceName = $this->fileManager->getOriginalName($file);
                        $name = $this->fileManager->save($file, FileType::VALIDATION);
                        $job->addValidationFile(new ValidationFile($job, $name, $sourceName));

                    } catch (FileException $exception) {
                        $this->addFlash(
                            'danger',
                            "Un problème est survenu lors de l'upload, veuillez réessayer.<br>"
                                ."Si le problème persiste veuillez contacter l'administrateur."
                        );

                        return $this->redirectToRoute('job_view', ['id' => $id]);
                    }
                }

                $this->manager->flush();

                $this->eventDispatcher->dispatch(
                    new AppJobEvent($job, JobEvent::VALIDATION_FILE_ADDED),
                    JobEvent::VALIDATION_FILE_ADDED->getEvent()
                );

                $this->addFlash(
                    'success',
                    sprintf(
                        'Les BAT du job <span class="font-bold">%s</span> ont été ajoutés',
                        $job->getCustomerReference()
                    )
                );
            }

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        return $this->render(
            'job/edit_validation_file.html.twig',
            [
                'form' => $form->createView(),
                'job' => $job,
            ]
        );
    }

    #[Route('/{fileId}/remove', name: '_remove')]
    public function removeValidationFile(
        int $id,
        int $fileId,
        ValidationFileRepository $fileRepository
    ): RedirectResponse {
        $file = $fileRepository->findWithRelations($fileId);
        $job = $file->getJob();

        if (!$this->isGranted('REMOVE_VALIDATION_FILE', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour supprimer ce BAT.");

            return $this->redirectToRoute('job_view', ['id' => $job->getId()]);
        }

        try {
            $job->removeValidationFile($file);
            $this->manager->persist($job);

            $this->fileManager->remove($file);
            $this->manager->flush();

            $this->eventDispatcher->dispatch(
                new AppJobEvent($job, JobEvent::VALIDATION_FILE_REMOVED),
                JobEvent::VALIDATION_FILE_REMOVED->getEvent()
            );

            $this->addFlash(
                'success',
                sprintf(
                    'Le BAT <span class="font-bold">%s</span> a été supprimé',
                    $file->getSourceName()
                )
            );

            return $this->redirectToRoute('job_view', ['id' => $job->getId()]);

        } catch (FileException $exception) {
            $this->addFlash('danger', "Erreur: Impossible de supprimer ce BAT.");

            return $this->redirectToRoute('job_view', ['id' => $job->getId()]);
        }
    }

    #[Route('/download', name: '_download')]
    public function download(int $id): BinaryFileResponse
    {
        $job = $this->jobRepository->findWithRelations($id);

        $response = new BinaryFileResponse($this->fileManager->getValidationFilesZip($job));
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'BAT ' . $job->getCustomerReference() . '.zip'
        );

        return $response;
    }

    #[Route('/{fileId}/download', name: '_download_one', requirements: ['fileId' => '\d+'])]
    public function downloadOne(int $fileId, ValidationFileRepository $fileRepository): BinaryFileResponse
    {
        $file = $fileRepository->findWithRelations($fileId);
        $response = new BinaryFileResponse($this->fileManager->getPath($file));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getSourceName());

        return $response;
    }
}
