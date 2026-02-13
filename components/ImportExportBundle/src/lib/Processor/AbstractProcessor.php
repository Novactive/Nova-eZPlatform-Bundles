<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor;

use AlmaviaCX\Bundle\IbexaImportExport\Component\AbstractComponent;

abstract class AbstractProcessor extends AbstractComponent implements ProcessorInterface
{
    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface|false
     */
    public function __invoke($item)
    {
        $processResult = $this->processItem($item);

        return null !== $processResult ? $processResult : $item;
    }

    /**
     * @param object|array $item
     */
    abstract public function processItem($item);
}
