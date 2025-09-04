<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;

trait ProcessorReferenceAggregationTrait
{
    /**
     * @var array<string, ComponentReference|ProcessorInterface<ProcessorOptions>>
     */
    protected array $processors = [];

    public function addProcessor(string $id, ComponentReference $processor): void
    {
        $this->processors[$id] = $processor;
    }

    /**
     * @return array<string, ComponentReference|ProcessorInterface<ProcessorOptions>>
     */
    public function getProcessors(): array
    {
        return $this->processors ?? [];
    }

    public function getProcessor(string $id): ?ComponentReference
    {
        return $this->processors[$id] ?? null;
    }
}
