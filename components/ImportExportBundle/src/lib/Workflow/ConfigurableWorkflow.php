<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface;

abstract class ConfigurableWorkflow extends AbstractWorkflow
{
    protected WorkflowConfiguration $workflowConfiguration;

    protected ReaderInterface $reader;

    /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterInterface[] */
    protected array $writers = [];

    /** @var \AlmaviaCX\Bundle\IbexaImportExport\Step\StepInterface[] */
    protected array $steps = [];

    public function __construct(
        WorkflowConfiguration $workflowConfiguration,
        ReaderInterface $reader,
        array $writers = [],
        array $steps = []
    ) {
        $this->workflowConfiguration = $workflowConfiguration;
        $this->reader = $reader;
        $this->writers = $writers;
        $this->steps = $steps;
    }

    public function getIdentifier(): string
    {
        return 'configurable';
    }

    protected function getReader(): ReaderInterface
    {
        return $this->reader;
    }

    protected function getWriters(): array
    {
        return $this->writers;
    }

    protected function getSteps(): array
    {
        return $this->steps;
    }

    public function getDefaultConfig(): WorkflowConfiguration
    {
        return new WorkflowConfiguration(
            'configurable',
            'Configurable workflow',
        );
    }
}
