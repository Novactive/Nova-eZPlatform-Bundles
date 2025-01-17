<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentReference;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;

/**
 * @property $value string|\AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\Source
 */
class IteratorProcessorOptions extends ProcessorOptions
{
    protected ComponentReference|ProcessorInterface $processor;

    /** @var string|\AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\Source */
    protected $value;

    public function setProcessor(string $class, ?ComponentOptions $options = null): void
    {
        $this->processor = new ComponentReference($class, $options);
    }

    /**
     * @param callable                                                                $buildComponentCallback
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Reader\InputAwareReaderOptions|null $runtimeProcessConfiguration
     */
    public function replaceComponentReferences(
        $buildComponentCallback,
        ?ComponentOptions $runtimeProcessConfiguration = null
    ): void {
        $options = $runtimeProcessConfiguration->processor ?? null;
        $this->processor = call_user_func(
            $buildComponentCallback,
            $this->processor,
            $options
        );
    }
}
