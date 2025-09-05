<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLogger;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;

class JobDebugger
{
    protected WorkflowExecutor $workflowExecutor;
    protected WorkflowRegistry $workflowRegistry;

    public function __construct(
        WorkflowExecutor $workflowExecutor,
        WorkflowRegistry $workflowRegistry
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->workflowExecutor = $workflowExecutor;
    }

    public function __invoke(Job $job, int $index): void
    {
        $workflow = $this->workflowRegistry->getWorkflow($job->getWorkflowIdentifier());
        $workflow->setLogger(new WorkflowLogger());
        $workflow->setDebug(true);
        $workflow->setOffset($index - 1);
        ($this->workflowExecutor)(
            $workflow,
            $job->getOptions(),
            1
        );
    }
}
