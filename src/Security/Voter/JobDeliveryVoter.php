<?php

namespace App\Security\Voter;

use App\Entity\Job;
use App\Enum\JobStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JobDeliveryVoter extends Voter
{
    private Job $job;
    public const UPDATE_DELIVERY = 'UPDATE_DELIVERY';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::UPDATE_DELIVERY])
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
            self::UPDATE_DELIVERY => $this->canSetDelivery(),
            default => false
        };
    }

    /**
     * @return bool
     */
    private function canSetDelivery(): bool
    {
        if (
            $this->security->isGranted('ROLE_PROJECT_MANAGER')
                && $this->job->getStatus() !== JobStatus::CREATED->getValue()
                && $this->job->getStatus() !== JobStatus::SHIPPED->getValue()
        ) {
            return true;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
