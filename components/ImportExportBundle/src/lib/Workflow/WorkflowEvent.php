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

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface $workflow
     */
    public function __construct(WorkflowInterface $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface
     */
    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflow;
    }
}
