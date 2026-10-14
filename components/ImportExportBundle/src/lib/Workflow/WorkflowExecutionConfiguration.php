<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\Aggregator\ProcessorAggregator;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptions;

class WorkflowExecutionConfiguration
{
    /**
     * @var \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface<ProcessorOptions>[]
     */
    protected array $processors = [];

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface<ReaderOptions> $reader
     */
    public function __construct(
        protected ReaderInterface $reader
    ) {
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface<ReaderOptions>
     */
    public function getReader(): ReaderInterface
    {
        return $this->reader;
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface<ProcessorOptions> $processor
     */
    public function addProcessor(string $id, ProcessorInterface $processor): void
    {
        $this->processors[$id] = $processor;
    }

    /**
     * @return array<string, WriterInterface<WriterOptions>>
     */
    public function getWriters(): array
    {
        return $this->findWriters($this->processors);
    }

    /**
     * @param array<string, ProcessorInterface<ProcessorOptions>> $processors
     *
     * @return array<string, WriterInterface<WriterOptions>>
     */
    protected function findWriters(array $processors): array
    {
        $writers = [];
        foreach ($processors as $id => $processor) {
            if ($processor instanceof WriterInterface) {
                $writers[$id] = $processor;
            }
            if ($processor instanceof ProcessorAggregator) {
                $writers = array_merge(
                    $writers,
                    $this->findWriters($processor->getProcessors())
                );
            }
        }

        return $writers;
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface<ProcessorOptions>[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }
}
