<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Aggregator;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorReferenceAggregationTrait;

/**
 * @property array<ComponentReference|ProcessorInterface<ProcessorOptions>> $processors
 * @property bool                                                           $errorBubbling
 */
class ProcessorAggregatorOptions extends ProcessorOptions
{
    use ProcessorReferenceAggregationTrait;

    protected bool $errorBubbling = true;

    public function merge($overrideOptions): static
    {
        dd($overrideOptions);
    }

    public function replaceComponentReferences(
        callable $componentBuilder,
        ?ComponentOptions $runtimeProcessConfiguration = null
    ): void {
        foreach ($this->processors as $key => $processor) {
            if ($processor instanceof ComponentReference) {
                /** @var ComponentReference|null $processorOptions */
                $processorOptions = $runtimeProcessConfiguration->processors[$key] ?? null;
                $this->processors[$key] = call_user_func(
                    $componentBuilder,
                    $processor,
                    $processorOptions?->getOptions()
                );
            }
        }
    }
}
