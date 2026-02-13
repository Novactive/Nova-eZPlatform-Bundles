<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface;

class PostJobRunEvent
{
    protected Job $job;
    protected WorkflowInterface $workflow;

    public function __construct(Job $job, WorkflowInterface $workflow)
    {
        $this->job = $job;
        $this->workflow = $workflow;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflow;
    }
}
