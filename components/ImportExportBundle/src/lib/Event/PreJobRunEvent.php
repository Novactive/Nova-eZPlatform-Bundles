<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PreJobRunEvent extends Event
{
    public function __construct(
        protected Execution $execution,
        protected WorkflowInterface $workflow
    ) {
    }

    public function getExecution(): Execution
    {
        return $this->execution;
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflow;
    }
}
