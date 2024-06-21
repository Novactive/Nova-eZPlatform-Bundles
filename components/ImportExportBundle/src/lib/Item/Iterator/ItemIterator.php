<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReaderIterator;
use Iterator;

class ItemIterator extends AbstractReaderIterator
{
    protected Iterator $innerIterator;
    protected IteratorItemTransformerInterface $itemTransformer;

    public function __construct(
        int $totalCount,
        Iterator $innerIterator,
        IteratorItemTransformerInterface $itemTransformer
    ) {
        $this->itemTransformer = $itemTransformer;
        $this->innerIterator = $innerIterator;
        parent::__construct($totalCount);
    }

    public function current()
    {
        return ($this->itemTransformer)($this->innerIterator->current());
    }

    public function next(): void
    {
        $this->innerIterator->next();
    }

    public function key(): int
    {
        return $this->innerIterator->key();
    }

    public function valid(): bool
    {
        return $this->innerIterator->valid();
    }

    public function rewind(): void
    {
        $this->innerIterator->rewind();
    }
}
