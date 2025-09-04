<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface;
use Exception;

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
