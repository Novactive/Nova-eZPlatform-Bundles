<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Aggregator;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorReferenceAggregationTrait;

/**
 * @property array<ComponentReference> $processors
 */
class ProcessorAggregatorOptions extends ProcessorOptions
{
    use ProcessorReferenceAggregationTrait;

    public function merge(ComponentOptions $overrideOptions): ComponentOptions
    {
        dd($overrideOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function replaceComponentReferences($buildComponentCallback): void
    {
        foreach ($this->processors as $key => $processor) {
            $this->processors[$key] = call_user_func(
                $buildComponentCallback,
                $processor
            );
        }
    }
}
