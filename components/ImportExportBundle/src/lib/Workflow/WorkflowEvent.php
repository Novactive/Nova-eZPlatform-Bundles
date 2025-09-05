<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

class WorkflowEvent
{
    public const PREPARE = 'workflow_prepare';
    public const START = 'workflow_start';
    public const PROGRESS = 'pre_item_process';
    public const FINISH = 'workflow_finish';

    protected WorkflowInterface $workflow;
    protected bool $continue = true;

    public function __construct(WorkflowInterface $workflow)
    {
        $this->workflow = $workflow;
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflow;
    }

    public function canContinue(): bool
    {
        return $this->continue;
    }

    public function setContinue(bool $continue): void
    {
        $this->continue = $continue;
    }
}
