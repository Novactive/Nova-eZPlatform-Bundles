<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\Source;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;

/**
 * @property string|Source                                           $value
 * @property ComponentReference|ProcessorInterface<ProcessorOptions> $processor
 */
class IteratorProcessorOptions extends ProcessorOptions
{
    /**
     * @var ComponentReference|ProcessorInterface<ProcessorOptions>
     */
    protected ComponentReference|ProcessorInterface $processor;

    protected Source|string $value;

    public function setProcessor(string $class, ?ComponentOptions $options = null): void
    {
        $this->processor = new ComponentReference($class, $options);
    }

    public function replaceComponentReferences(
        callable $componentBuilder,
        ?ComponentOptions $runtimeProcessConfiguration = null
    ): void {
        if ($this->processor instanceof ComponentReference) {
            /** @var ComponentReference|null $processorOptions */
            $processorOptions = $runtimeProcessConfiguration->processor ?? null;
            $this->processor = call_user_func(
                $componentBuilder,
                $this->processor,
                $processorOptions?->getOptions()
            );
        }
    }
}
