<?php

namespace App\Controller\Api;

use App\Entity\Job;
use App\Entity\ValidationFile;
use App\Enum\FileType;
use App\Enum\JobEvent;
use App\Enum\JobStatus;
use App\Event\AppJobEvent;
use App\Repository\JobRepository;
use App\Service\AppFileManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/job/{id}')]
class ApiJobController extends AbstractController
{
    private ObjectManager $manager;

    public function __construct(
        private SerializerInterface $serializer,
        private JobRepository $jobRepository,
        private EventDispatcherInterface $eventDispatcher,
        ManagerRegistry $managerRegistry,
    ) {
        $this->manager = $managerRegistry->getManager();
    }

    #[Route('', methods: ['GET'])]
    public function getJobInfo(int $id): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$job instanceof Job) {
            return new Response($this->serializer->serialize(['message' => 'Not found'], 'xml'), 404);
        }

        return new Response(
            $this->serializer->serialize($job, 'xml', ['groups' => 'api_job_get']),
            200,
            headers: ['Content-Type' => 'text/xml']
        );
    }

    #[Route('/validation-file', methods: ['POST'])]
    public function postJobValidationFile(int $id, Request $request, AppFileManager $fileManager)
    {
        $job = $this->jobRepository->find($id);

        if (!$job instanceof Job) {
            return new Response($this->serializer->serialize(['message' => 'Not found'], 'xml'), 404);
        }

        if ($job->getStatus() !== JobStatus::SENT->getValue()) {
            return new Response(
                $this->serializer->serialize(['message' => 'The job is not in SENT status'], 'xml'), 400
            );
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            return new Response(
                $this->serializer->serialize(['message' => 'File not found'], 'xml'), 400
            );
        }

        if ($file) {
            try {
                $originalName = $fileManager->getOriginalName($file);
                $safeName = $fileManager->save($file, FileType::VALIDATION);

                $job->addValidationFile(new ValidationFile($job, $safeName, $originalName));

            } catch (FileException $exception) {
                return new Response(
                    $this->serializer->serialize(['message' => $exception->getMessage()], 'xml'), 500
                );
            }
        }

        $this->manager->flush();

        $this->eventDispatcher->dispatch(
            new AppJobEvent($job, JobEvent::VALIDATION_FILE_ADDED),
            JobEvent::VALIDATION_FILE_ADDED->getEvent()
        );

        return new Response(
            $this->serializer->serialize(['message' => 'Validation file has been uploaded successfully'], 'xml'), 201
        );
    }
}
