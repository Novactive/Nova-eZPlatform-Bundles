<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorReferenceAggregationTrait;

class WorkflowProcessConfiguration
{
    use ProcessorReferenceAggregationTrait;

    protected ?ComponentReference $reader = null;

    public function setReader(ComponentReference $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param array<ComponentReference> $processors
     */
    public function setProcessors(array $processors): void
    {
        $this->processors = $processors;
    }

    public function getReader(): ?ComponentReference
    {
        return $this->reader;
    }
}
