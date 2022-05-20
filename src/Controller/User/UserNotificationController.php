<?php

namespace App\Controller\User;

use App\Entity\UserEventNotification;
use App\Enum\JobEvent;
use App\Repository\UserEventNotificationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/settings/notification/{event}', name: 'user_settings_notification')]
class UserNotificationController extends AbstractController
{
    private ObjectManager $manager;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private UserEventNotificationRepository $notificationRepository,
    ) {
        $this->manager = $managerRegistry->getManager();
    }

    /**
     * Add an event notification to a user
     *
     * @param string $event
     * @return JsonResponse
     */
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function add(string $event): JsonResponse
    {
        $notification = $this->notificationRepository->findOneBy(['user' => $this->getUser(), 'event' => $event]);

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(
                ['message' => 'Error: notifications can only be performed by authenticated users.'],
                401
            );
        }

        if ($notification instanceof UserEventNotification) {
            return new JsonResponse(['message' => 'Error: notification already exists.'], 400);
        }

        $notification = new UserEventNotification($this->getUser(), JobEvent::from($event));
        $this->manager->persist($notification);
        $this->manager->flush();

        return new JsonResponse(['message' => 'The notification has been added'], 201);
    }

    /**
     * Remove an event notification from a user
     *
     * @param string $event
     * @return JsonResponse
     */
    #[Route('/remove', name: '_remove', methods: ['DELETE'])]
    public function remove(string $event): JsonResponse
    {
        $notification = $this->notificationRepository->findOneBy(['user' => $this->getUser(), 'event' => $event]);

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(
                ['message' => 'Error: notifications can only be performed by authenticated users.'],
                401
            );
        }

        if (!$notification instanceof UserEventNotification) {
            return new JsonResponse(['message' => 'Error: notification do not exist.'], 400);
        }

        $this->manager->remove($notification);
        $this->manager->flush();

        return new JsonResponse(['message' => 'The notification has been removed'], 204);
    }
}
