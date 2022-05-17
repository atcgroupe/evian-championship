<?php

namespace App\Security\Voter;

use App\Entity\Job;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JobStatusRequestVoter extends Voter
{
    private Job $job;
    public const REQUEST_UPDATE_STATUS_TO_CANCELED = 'REQUEST_UPDATE_STATUS_TO_CANCELED';
    public const REQUEST_UPDATE_STATUS_TO_CREATED = 'REQUEST_UPDATE_STATUS_TO_CREATED';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
                self::REQUEST_UPDATE_STATUS_TO_CANCELED,
                self::REQUEST_UPDATE_STATUS_TO_CREATED,
            ]) && $subject instanceof Job;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $this->job = $subject;

        return match ($attribute) {
            self::REQUEST_UPDATE_STATUS_TO_CANCELED => $this->canRequestUpdateToCanceled(),
            self::REQUEST_UPDATE_STATUS_TO_CREATED => $this->canRequestUpdateToCreated(),
            default => false
        };
    }

    /**
     * @return bool
     */
    private function canRequestUpdateToCanceled(): bool
    {
        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() > 1 &&
            $this->job->getStatus() < 7
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canRequestUpdateToCreated(): bool
    {
        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() > 1 &&
            $this->job->getStatus() < 6
        ) {
            return true;
        }

        return false;
    }
}
