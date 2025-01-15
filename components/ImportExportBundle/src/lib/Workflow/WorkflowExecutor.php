<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;
use Exception;

class WorkflowExecutor
{
    protected ComponentBuilder $componentBuilder;

    public function __construct(ComponentBuilder $componentBuilder)
    {
        $this->componentBuilder = $componentBuilder;
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface             $workflow
     * @param array{reader?: ReaderOptions, processors?: array<mixed, ProcessorOptions>} $runtimeProcessConfiguration
     */
    public function __invoke(
        WorkflowInterface $workflow,
        array $runtimeProcessConfiguration,
        int $batchLimit = -1
    ): void {
        $workflow->setConfiguration(
            $this->buildRunConfiguration(
                $workflow,
                $runtimeProcessConfiguration
            )
        );

        ($workflow)($batchLimit);
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface             $workflow
     * @param array{reader?: ReaderOptions, processors?: array<mixed, ProcessorOptions>} $runtimeProcessConfiguration
     *
     * @throws \Exception
     *
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutionConfiguration
     */
    protected function buildRunConfiguration(
        WorkflowInterface $workflow,
        array $runtimeProcessConfiguration
    ): WorkflowExecutionConfiguration {
        $baseConfiguration = $workflow->getDefaultConfig();

        $processConfiguration = $baseConfiguration->getProcessConfiguration();
        $reader = ($this->componentBuilder)(
            $processConfiguration->getReader(),
            $runtimeProcessConfiguration['reader'] ?? null
        );
        if (!$reader instanceof ReaderInterface) {
            throw new Exception('Reader not instance of '.ReaderInterface::class);
        }
        $executionConfiguration = new WorkflowExecutionConfiguration($reader);

        $processorsConfiguration = $processConfiguration->getProcessors();
        foreach ($processorsConfiguration as $index => $processorConfiguration) {
            $processor = ($this->componentBuilder)(
                $processorConfiguration,
                $runtimeProcessConfiguration['processors'][$index] ?? null
            );
            if ($processor instanceof ProcessorInterface) {
                $executionConfiguration->addProcessor($processor);
            }
        }

        return $executionConfiguration;
    }
}
