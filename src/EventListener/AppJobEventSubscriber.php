<?php

namespace App\EventListener;

use App\Enum\JobEvent;
use App\Event\AppJobEvent;
use App\Service\JobLogManager;
use App\Service\Notification\NotificationDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppJobEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JobLogManager $logManager,
        private NotificationDispatcher $notificationDispatcher,
    ) {}

    public static function getSubscribedEvents()
    {
        return [
            JobEvent::CREATED->getEvent() => 'addJobLog',
            JobEvent::INFO_UPDATED->getEvent() => 'addJobLog',
            JobEvent::JOB_FILE_ADDED->getEvent() => 'addJobLog',
            JobEvent::JOB_FILE_REMOVED->getEvent() => 'addJobLog',
            JobEvent::VALIDATION_FILE_ADDED->getEvent() => 'addJobLog',
            JobEvent::VALIDATION_FILE_REMOVED->getEvent() => 'addJobLog',
            JobEvent::DELIVERY_INFO_UPDATED->getEvent() => 'onDeliveryInfoUpdated',
            JobEvent::SENT->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::PAO->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::APPROVAL->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::APPROVED->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::UPDATE->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::REJECTED->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::PRODUCTION->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::SENT_FOR_UPDATE->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::REQUEST_UPDATE->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::REQUEST_CANCEL->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::CANCELED->getEvent() => [['addJobUpdateLog'], ['dispatchNotification']],
            JobEvent::STATUS_UPDATED->getEvent() => 'addJobUpdateLog',
            JobEvent::DELETED->getEvent() => 'dispatchNotification',
        ];
    }

    public function addJobLog(AppJobEvent $event): void
    {
        $message = match ($event->getEvent()) {
            JobEvent::CREATED => 'Création du job',
            JobEvent::INFO_UPDATED => 'Modification des informations.',
            JobEvent::JOB_FILE_ADDED => 'Ajout du fichier de production.',
            JobEvent::JOB_FILE_REMOVED => 'Suppression du fichier de production.',
            JobEvent::VALIDATION_FILE_ADDED => 'Ajout des fichiers BAT',
            JobEvent::VALIDATION_FILE_REMOVED => "Suppression d'un fichier BAT",
        };

        $this->logManager->addLog($event->getJob(), $message);
    }

    /**
     * @param AppJobEvent $event
     * @return void
     */
    public function onDeliveryInfoUpdated(AppJobEvent $event): void
    {
        $this->logManager->addDeliveryLog($event->getJob());
    }

    /**
     * @param AppJobEvent $event
     * @return void
     */
    public function addJobUpdateLog(AppJobEvent $event): void
    {
        $this->logManager->addUpdateLog($event->getJob(), $event->getPreStatus(), $event->getPostStatus());
    }

    /**
     * @param AppJobEvent $event
     * @return void
     */
    public function dispatchNotification(AppJobEvent $event): void
    {
        $this->notificationDispatcher->dispatch($event->getEvent(), $event->getJob());
    }
}
