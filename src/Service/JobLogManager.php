<?php

namespace App\Service;

use App\Entity\Job;
use App\Entity\JobLog;
use App\Entity\User;
use App\Enum\JobStatus;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Security;

class JobLogManager
{
    Private ObjectManager $manager;

    public function __construct(
        private Security $security,
        private ManagerRegistry $managerRegistry,
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
        /** @var User $user */
        $user = $this->security->getUser();
        $userName = ($user instanceof User) ? $user->getDisplayName('log') : 'API Flow';

        $log = new JobLog($job, $userName, $action);
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
            return 'Envoi du job ?? ATC';
        }

        if ($preStatus === JobStatus::SENT && $postStatus === JobStatus::PAO) {
            return 'Prise en charge du job en PAO';
        }

        if ($preStatus === JobStatus::PAO && $postStatus === JobStatus::APPROVAL) {
            return 'Envoi du job pour Validation BAT';
        }

        if ($preStatus === JobStatus::APPROVAL && $postStatus === JobStatus::APPROVED) {
            return 'BAT approuv??';
        }

        if ($preStatus === JobStatus::APPROVAL && $postStatus === JobStatus::REJECTED) {
            return 'BAT rejet??';
        }

        if ($preStatus === JobStatus::APPROVAL && $postStatus === JobStatus::CREATED) {
            return 'Demande de modification du job par le client apr??s soumission du BAT';
        }

        if ($preStatus === JobStatus::APPROVED && $postStatus === JobStatus::PRODUCTION) {
            return 'Envoi du Job en production';
        }

        if ($preStatus === JobStatus::PRODUCTION && $postStatus === JobStatus::SHIPPED) {
            return 'Le job a ??t?? exp??di??';
        }

        if ($postStatus === JobStatus::STANDBY) {
            return sprintf(
                'Le job a ??t?? mis en Stand-By.%s ?? l\'??tape "%s"',
                $job->getStandbyComment(),
                $preStatus->getLabel()
            );
        }

        if ($preStatus === JobStatus::STANDBY && $postStatus === JobStatus::CREATED) {
            return 'Le job a ??t?? renvoy?? au client pour modification';
        }

        if ($postStatus === JobStatus::CANCELED) {
            return sprintf(
                'Le job a ??t?? annul??. Etape avant annulation:"%s"',
                $preStatus->getLabel()
            );
        }

        if ($preStatus === JobStatus::CANCELED) {
            return sprintf(
                'Le job a ??t?? r??activ?? apr??s annulation. Etape apr??s annulation:"%s"',
                $postStatus->getLabel()
            );
        }

        return sprintf(
            'Changement de statut du job de statut "%s" ?? "%s"',
            $preStatus->getLabel(),
            $postStatus->getLabel()
        );
    }
}
