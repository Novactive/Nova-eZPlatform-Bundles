<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\Aggregator\ProcessorAggregator;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterInterface;
use Generator;

class WorkflowExecutionConfiguration
{
    protected ReaderInterface $reader;
    /** @var \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface[] */
    protected array $processors = [];

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function getReader(): ReaderInterface
    {
        return $this->reader;
    }

    public function addProcessor(ProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterInterface[]
     */
    public function getWriters(): array
    {
        return iterator_to_array($this->findWriters($this->processors));
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface[] $processors
     */
    protected function findWriters(array $processors): Generator
    {
        foreach ($processors as $processor) {
            if ($processor instanceof WriterInterface) {
                yield $processor;
            }
            if ($processor instanceof ProcessorAggregator) {
                $this->findWriters($processor->getProcessors());
            }
        }
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }
}
