<?php

namespace App\Controller\User;

use App\Enum\JobEventGroup;
use App\Service\Notification\SwitchEventNotificationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/settings', name: 'user_settings')]
class UserSettingsController extends AbstractController
{
    #[Route('', name: '_index')]
    public function index(SwitchEventNotificationManager $notificationManager): Response
    {
        return $this->render(
            'user/settings/index.html.twig',
            [
                'customerSwitchEvents' => $notificationManager->getUserSwitchEventNotifications(JobEventGroup::CUSTOMER),
                'companySwitchEvents' => $notificationManager->getUserSwitchEventNotifications(JobEventGroup::COMPANY),
            ]
        );
    }
}
