<?php

namespace App\Security\Voter;

use App\Entity\Job;
use App\Enum\JobStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JobFileVoter extends Voter
{
    private Job $job;
    public const ADD_JOB_FILE = 'ADD_JOB_FILE';
    public const REMOVE_JOB_FILE = 'REMOVE_JOB_FILE';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ADD_JOB_FILE, self::REMOVE_JOB_FILE])
            && $subject instanceof Job;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $this->job = $subject;

        return match ($attribute) {
            self::ADD_JOB_FILE => $this->canAddJobFile(),
            self::REMOVE_JOB_FILE => $this->canRemoveJobFile(),
            default => false
        };
    }

    /**
     * @return bool
     */
    private function canAddJobFile(): bool
    {
        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() === JobStatus::CREATED->getValue() &&
            count($this->job->getJobFiles()) == 0
        ) {
            return true;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canRemoveJobFile(): bool
    {
        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() === JobStatus::CREATED->getValue()
        ) {
            return true;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
