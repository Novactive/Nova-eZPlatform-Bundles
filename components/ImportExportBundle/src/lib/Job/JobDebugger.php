<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLogger;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;

class JobDebugger
{
    public function __construct(
        protected WorkflowExecutor $workflowExecutor,
        protected WorkflowRegistry $workflowRegistry
    ) {
    }

    public function __invoke(Execution $execution, int $index): void
    {
        $workflow = $this->workflowRegistry->getWorkflow($execution->getWorkflowIdentifier());
        $workflow->setLogger(new WorkflowLogger());
        $workflow->setDebug(true);

        $state = $execution->getWorkflowState();
        $state->setOffset($index - 1);
        $workflow->setState($state);

        ($this->workflowExecutor)(
            $workflow,
            $execution->getOptions(),
            1
        );
    }
}
