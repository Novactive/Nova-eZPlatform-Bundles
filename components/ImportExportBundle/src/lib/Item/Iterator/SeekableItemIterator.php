<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

class SeekableItemIterator extends ItemIterator implements \SeekableIterator
{
    public function __construct(
        int $totalCount,
        \SeekableIterator $innerIterator,
        IteratorItemTransformerInterface $itemTransformer
    ) {
        parent::__construct($totalCount, $innerIterator, $itemTransformer);
    }

    public function seek($offset): void
    {
        $this->innerIterator->seek($offset);
    }
}
