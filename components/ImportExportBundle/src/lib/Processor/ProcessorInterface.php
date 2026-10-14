<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;

/**
 * @phpstan-type ProcessableItem mixed
 * @template TProcessorOptions of ProcessorOptions
 * @extends ComponentInterface<TProcessorOptions>
 */
interface ProcessorInterface extends ComponentInterface
{
    /**
     * Return "false" to stop further processing of the item.
     * Return "null" if the item has not been processed and should be passed to the next processor.
     * Else, return the processed item.
     *
     * @param ProcessableItem $item
     *
     * @return ProcessableItem|ItemAccessorInterface|false
     */
    public function __invoke($item): mixed;

    public function setIdentifier(string $identifier): void;
}
