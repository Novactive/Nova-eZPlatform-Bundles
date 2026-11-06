<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface;
use Exception;

class RunConfigurationBuilder
{
    public function __construct(
        protected ComponentBuilder $componentBuilder
    ) {
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(
        WorkflowInterface $workflow,
        ExecutionOptions $executionOptions
    ): WorkflowExecutionConfiguration {
        $baseConfiguration = $workflow->getDefaultConfig();

        $processConfiguration = $baseConfiguration->getProcessConfiguration();
        $reader = ($this->componentBuilder)(
            $processConfiguration->getReader(),
            $executionOptions->getReaderOptions()
        );
        if (!$reader instanceof ReaderInterface) {
            throw new Exception('Reader not instance of '.ReaderInterface::class);
        }
        $executionConfiguration = new WorkflowExecutionConfiguration($reader);

        $processorsConfiguration = $processConfiguration->getProcessors();
        foreach ($processorsConfiguration as $id => $processorConfiguration) {
            $processor = ($this->componentBuilder)(
                $processorConfiguration,
                $executionOptions->getProcessorOptions($id)
            );
            if ($processor instanceof ProcessorInterface) {
                $executionConfiguration->addProcessor($id, $processor);
            }
        }

        return $executionConfiguration;
    }
}
