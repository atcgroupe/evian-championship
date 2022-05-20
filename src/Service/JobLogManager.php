<?php

namespace App\Service;

use App\Entity\Job;
use App\Entity\JobLog;
use App\Enum\JobStatus;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Security;

class JobLogManager
{
    Private ObjectManager $manager;

    public function __construct(
        private Security $security,
        private ManagerRegistry $managerRegistry
    ) {
        $this->manager = $this->managerRegistry->getManager();
    }

    /**
     * @param Job $job
     * @param string $action
     * @return void
     */
    public function addLog(Job $job, string $action): void
    {
        $log = new JobLog($job, $this->security->getUser(), $action);
        $job->addJobLog($log);
        $this->manager->persist($log);
        $this->manager->flush();
    }

    /**
     * @param Job $job
     * @param JobStatus $preStatus
     * @param JobStatus $postStatus
     * @return void
     */
    public function addUpdateLog(Job $job, JobStatus $preStatus, JobStatus $postStatus)
    {
        $this->addLog($job, $this->getUpdateAction($job, $preStatus, $postStatus));
    }

    /**
     * @param Job $job
     * @return void
     */
    public function addDeliveryLog(Job $job)
    {
        $this->addLog(
            $job,
            sprintf(
                'Modification des informations de livraison. Livraison %s le %s',
                $job->getDelivery()->getTitle(),
                $job->getDelivery()->getDate()->format('d/m')
            )
        );
    }

    /**
     * @param Job $job
     * @param JobStatus $preStatus
     * @param JobStatus $postStatus
     * @return string
     */
    private function getUpdateAction(Job $job, JobStatus $preStatus, JobStatus $postStatus): string
    {
        if ($preStatus === JobStatus::CREATED && $postStatus === JobStatus::SENT) {
            return 'Envoi du job à ATC';
        }

        if ($preStatus === JobStatus::SENT && $postStatus === JobStatus::APPROVAL) {
            return 'Envoi du job pour Validation BAT';
        }

        if ($preStatus === JobStatus::APPROVAL && $postStatus === JobStatus::APPROVED) {
            return 'BAT approuvé';
        }

        if ($preStatus === JobStatus::APPROVAL && $postStatus === JobStatus::REJECTED) {
            return 'BAT rejeté';
        }

        if ($preStatus === JobStatus::APPROVAL && $postStatus === JobStatus::CREATED) {
            return 'Demande de modification du job par le client après soumission du BAT';
        }

        if ($preStatus === JobStatus::APPROVED && $postStatus === JobStatus::PRODUCTION) {
            return 'Envoi du Job en production';
        }

        if ($preStatus === JobStatus::PRODUCTION && $postStatus === JobStatus::SHIPPED) {
            return 'Le job a été expédié';
        }

        if ($postStatus === JobStatus::STANDBY) {
            return sprintf(
                'Le job a été mis en Stand-By.%s à l\'étape "%s"',
                $job->getStandbyComment(),
                $preStatus->getLabel()
            );
        }

        if ($preStatus === JobStatus::STANDBY && $postStatus === JobStatus::CREATED) {
            return 'Le job a été renvoyé au client pour modification';
        }

        if ($postStatus === JobStatus::CANCELED) {
            return sprintf(
                'Le job a été annulé. Etape avant annulation:"%s"',
                $preStatus->getLabel()
            );
        }

        if ($preStatus === JobStatus::CANCELED) {
            return sprintf(
                'Le job a été réactivé après annulation. Etape après annulation:"%s"',
                $postStatus->getLabel()
            );
        }

        return sprintf(
            'Changement de statut du job de statut "%s" à "%s"',
            $preStatus->getLabel(),
            $postStatus->getLabel()
        );
    }
}
