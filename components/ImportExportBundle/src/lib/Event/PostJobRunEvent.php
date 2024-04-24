<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Result\Result;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface;

class PostJobRunEvent
{
    protected Job $job;
    protected WorkflowInterface $workflow;
    protected Result $result;

    public function __construct(Job $job, WorkflowInterface $workflow, Result $result)
    {
        $this->job = $job;
        $this->workflow = $workflow;
        $this->result = $result;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflow;
    }

    public function getResult(): Result
    {
        return $this->result;
    }
}
