<?php

namespace App\Security\Voter;

use App\Entity\Job;
use App\Enum\JobStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JobStatusVoter extends Voter
{
    private Job $job;
    public const UPDATE_STATUS = 'UPDATE_STATUS';
    public const UPDATE_STATUS_FROM_CREATED_TO_SENT = 'UPDATE_STATUS_FROM_CREATED_TO_SENT';
    public const UPDATE_STATUS_FROM_SENT_TO_APPROVAL = 'UPDATE_STATUS_FROM_SENT_TO_APPROVAL';
    public const UPDATE_STATUS_FROM_APPROVAL = 'UPDATE_STATUS_FROM_APPROVAL';
    public const UPDATE_STATUS_FROM_APPROVED_TO_PRODUCTION = 'UPDATE_STATUS_FROM_APPROVED_TO_PRODUCTION';
    public const UPDATE_STATUS_TO_CANCELED = 'UPDATE_STATUS_TO_CANCELED';
    public const UPDATE_STATUS_TO_CREATED = 'UPDATE_STATUS_TO_CREATED';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, $subject): bool
    {
        return in_array(
            $attribute, [
                self::UPDATE_STATUS,
                self::UPDATE_STATUS_FROM_CREATED_TO_SENT,
                self::UPDATE_STATUS_FROM_SENT_TO_APPROVAL,
                self::UPDATE_STATUS_FROM_APPROVAL,
                self::UPDATE_STATUS_FROM_APPROVED_TO_PRODUCTION,
                self::UPDATE_STATUS_TO_CANCELED,
                self::UPDATE_STATUS_TO_CREATED,
            ])
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
            self::UPDATE_STATUS => $this->canUpdateStatus(),
            self::UPDATE_STATUS_FROM_CREATED_TO_SENT => $this->canUpdateFromCreatedToSent(),
            self::UPDATE_STATUS_FROM_SENT_TO_APPROVAL => $this->canUpdateFromSentToApproval(),
            self::UPDATE_STATUS_FROM_APPROVAL => $this->canUpdateFromApproval(),
            self::UPDATE_STATUS_FROM_APPROVED_TO_PRODUCTION => $this->canUpdateFromApprovedToProduction(),
            self::UPDATE_STATUS_TO_CANCELED => $this->canUpdateStatusToCanceled(),
            self::UPDATE_STATUS_TO_CREATED => $this->canUpdateStatusToCreated(),
            default => false
        };
    }

    /**
     * @return bool
     */
    private function canUpdateStatus(): bool
    {
        if (
            $this->security->isGranted('ROLE_PROJECT_MANAGER') &&
            $this->job->getStatus() !== 1 &&
            $this->job->getStatus() !== 3
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
    private function canUpdateFromCreatedToSent(): bool
    {
        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() === 1 &&
            count($this->job->getJobFiles()) > 0
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canUpdateFromSentToApproval(): bool
    {
        if (!$this->security->isGranted('ROLE_COMPANY_USER')) {
            return false;
        }

        if (count($this->job->getValidationFiles()) === 0) {
            return false;
        }

        if (
            $this->job->getStatus() == JobStatus::SENT->getValue() ||
            $this->job->getStatus() == JobStatus::REJECTED->getValue()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canUpdateFromApproval(): bool
    {
        if (
            $this->security->isGranted('ROLE_CUSTOMER') &&
            $this->job->getStatus() === 3 &&
            count($this->job->getValidationFiles()) > 0
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canUpdateFromApprovedToProduction(): bool
    {
        if (
            $this->security->isGranted('ROLE_COMPANY_USER') &&
            $this->job->getStatus() == JobStatus::APPROVED->getValue()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canUpdateStatusToCanceled(): bool
    {
        if (
            $this->security->isGranted('ROLE_PROJECT_MANAGER') &&
            (
                ($this->job->getStatus() > 1 && $this->job->getStatus() < 7) ||
                $this->job->getStatus() == 8
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canUpdateStatusToCreated(): bool
    {
        if (
            $this->security->isGranted('ROLE_COMPANY_USER') &&
            $this->job->getStatus() == JobStatus::STANDBY->getValue()
        ) {
            return true;
        }

        return false;
    }
}
