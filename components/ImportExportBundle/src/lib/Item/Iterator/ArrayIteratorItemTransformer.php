<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;

/**
 * @template TSourceItem
 * @template TResultItem
 * @implements IteratorItemTransformerInterface<TSourceItem, TResultItem>
 */
class ArrayIteratorItemTransformer implements IteratorItemTransformerInterface
{
    public function __invoke(mixed $item): mixed
    {
        return new ArrayAccessor($item);
    }
}
