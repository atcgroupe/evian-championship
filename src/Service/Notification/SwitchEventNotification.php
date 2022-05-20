<?php

namespace App\Service\Notification;

class SwitchEventNotification
{
    public function __construct(
        private int $eventId,
        private string $label,
        private int $state,
    ) {}

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }
}
