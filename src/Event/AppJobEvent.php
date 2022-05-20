<?php

namespace App\Event;

use App\Entity\Job;
use App\Enum\JobEvent;
use App\Enum\JobStatus;
use Symfony\Contracts\EventDispatcher\Event;

class AppJobEvent extends Event
{
    public function __construct(
        protected Job $job,
        protected JobEvent $event,
        protected ?JobStatus $preStatus = null,
        protected ?JobStatus $postStatus = null,
    ) {}

    /**
     * @return Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }

    /**
     * @return JobEvent
     */
    public function getEvent(): JobEvent
    {
        return $this->event;
    }

    /**
     * @return JobStatus|null
     */
    public function getPreStatus(): JobStatus|null
    {
        return $this->preStatus;
    }

    /**
     * @return JobStatus|null
     */
    public function getPostStatus(): JobStatus|null
    {
        return $this->postStatus;
    }
}
