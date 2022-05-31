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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function getJobInfo(int $id): JsonResponse
    {
        $job = $this->jobRepository->find($id);

        if (!$job instanceof Job) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }

        return new JsonResponse(
            $this->serializer->serialize($job, 'json', ['groups' => 'api_job_get']),
            status: 200,
            json: true
        );
    }

    #[Route('/validation-file', methods: ['POST'])]
    public function postJobValidationFile(int $id, Request $request, AppFileManager $fileManager)
    {
        $job = $this->jobRepository->find($id);

        if (!$job instanceof Job) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }

        if ($job->getStatus() !== JobStatus::SENT->getValue()) {
            return new JsonResponse(['message' => 'The job is not in SENT status'], 400);
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if ($file) {
            try {
                $originalName = $fileManager->getOriginalName($file);
                $safeName = $fileManager->save($file, FileType::VALIDATION);

                $job->addValidationFile(new ValidationFile($job, $safeName, $originalName));

            } catch (FileException $exception) {
                return new JsonResponse(['message' => $exception->getMessage()], 400);
            }
        }

        $this->manager->flush();

        $this->eventDispatcher->dispatch(
            new AppJobEvent($job, JobEvent::VALIDATION_FILE_ADDED),
            JobEvent::VALIDATION_FILE_ADDED->getEvent()
        );

        return new JsonResponse(
            ['message' => 'Validation file has been uploaded successfully'],
            201
        );
    }
}
