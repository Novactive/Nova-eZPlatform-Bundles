<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

/**
 * @template TSourceItem
 * @template TResultItem
 */
interface IteratorItemTransformerInterface
{
    /**
     * @param TSourceItem $item
     *
     * @return TResultItem
     */
    public function __invoke(mixed $item): mixed;
}
