<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;

trait ProcessorReferenceAggregationTrait
{
    /**
     * @var array<ComponentReference>
     */
    protected array $processors = [];

    public function addProcessor(ComponentReference $processor): void
    {
        $this->processors[] = $processor;
    }

    /**
     * @return array<ComponentReference>
     */
    public function getProcessors(): array
    {
        return $this->processors ?? [];
    }
}
