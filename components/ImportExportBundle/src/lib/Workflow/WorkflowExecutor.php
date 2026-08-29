<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;

class WorkflowExecutor
{
    public function __construct(
        protected RunConfigurationBuilder $runConfigurationBuilder
    ) {
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(
        WorkflowInterface $workflow,
        ExecutionOptions $executionOptions,
        int $batchLimit = -1
    ): void {
        $workflow->setConfiguration(
            ($this->runConfigurationBuilder)(
                $workflow,
                $executionOptions
            )
        );

        ($workflow)($batchLimit);
    }
}
