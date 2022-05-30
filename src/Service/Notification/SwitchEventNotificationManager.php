<?php

namespace App\Service\Notification;

use App\Enum\JobEvent;
use App\Enum\JobEventGroup;
use App\Repository\UserEventNotificationRepository;
use Symfony\Component\Security\Core\Security;

class SwitchEventNotificationManager
{
    public function __construct(
        private Security $security,
        private UserEventNotificationRepository $notificationRepository,
    ) {}

    /**
     * Returns an array of SwitchEventNotifications for the authenticated user
     *
     * @param JobEventGroup $group
     * @return SwitchEventNotification[]
     */
    public function getUserSwitchEventNotifications(JobEventGroup $group): array
    {
        $userNotifications = $this->notificationRepository->findBy(['user' => $this->security->getUser()]);
        $userState = [];
        foreach ($userNotifications as $notification) {
            $userState[] = $notification->getEvent();
        }

        $state = [];
        foreach (JobEvent::cases() as $case) {
            if ($case->getGroup() === $group || $case->getGroup() === JobEventGroup::ALL)
            $state[] = new SwitchEventNotification(
                $case->getValue(),
                $case->getLabel(),
                in_array($case->getValue(), $userState) ? '1' : '0',
            );
        }

        return $state;
    }
}
