<?php

namespace App\Security\Voter;

use App\Entity\Job;
use App\Enum\JobStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JobValidationFileVoter extends Voter
{
    private Job $job;
    public const ADD_VALIDATION_FILE = 'ADD_VALIDATION_FILE';
    public const REMOVE_VALIDATION_FILE = 'REMOVE_VALIDATION_FILE';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ADD_VALIDATION_FILE, self::REMOVE_VALIDATION_FILE])
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
            self::ADD_VALIDATION_FILE => $this->canAddValidationFile(),
            self::REMOVE_VALIDATION_FILE => $this->canRemoveValidationFile(),
            default => false
        };
    }

    /**
     * @return bool
     */
    private function canAddValidationFile(): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$this->security->isGranted('ROLE_COMPANY_USER')) {
            return false;
        }

        if ($this->job->getStatus() === JobStatus::PAO->getValue()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canRemoveValidationFile(): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$this->security->isGranted('ROLE_COMPANY_USER')) {
            return false;
        }

        if ($this->job->getStatus() === JobStatus::PAO->getValue()) {
            return true;
        }

        return false;
    }
}
