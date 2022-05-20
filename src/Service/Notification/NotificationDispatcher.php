<?php

namespace App\Service\Notification;

use App\Entity\Job;
use App\Enum\JobEvent;
use App\Repository\UserEventNotificationRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class NotificationDispatcher
{
    public function __construct(
        private MailerInterface $mailer,
        private UserEventNotificationRepository $eventNotificationRepository,
        private Environment $twig,
    ) {}

    public function dispatch(JobEvent $event, Job $job)
    {
        $addresses = $this->getEventUsersAddresses($event);

        if (empty($addresses)) {
            return;
        }

        $template = $this->twig->render(
            'component/notification/email.html.twig',
            [
                'message' => $event->getEmailMessage(),
                'job' => $job,
            ]
        );

        $email = (new Email())
            ->from(Address::create('JobFlow ATC Groupe <web2print@atc-groupe.com>'))
            ->to(...$addresses)
            ->subject('Notification')
            ->html($template)
        ;

        $this->mailer->send($email);
    }

    private function getEventUsersAddresses(JobEvent $event): array
    {
        $notifications = $this->eventNotificationRepository->findBy(['event' => $event->getValue()]);
        $addresses = [];

        foreach ($notifications as $notification) {
            $addresses[] = $notification->getUser()->getEmail();
        }

        return $addresses;
    }
}
