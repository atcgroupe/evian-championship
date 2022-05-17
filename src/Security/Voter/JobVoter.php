<?php

namespace App\Security\Voter;

use App\Entity\Job;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JobVoter extends Voter
{
    private Job $job;
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
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
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            default => false
        };
    }

    /**
     * @return bool
     */
    private function canEdit(): bool
    {
        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() === 1
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
    private function canDelete(): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() === 1
        ) {
            return true;
        }

        return false;
    }
}
