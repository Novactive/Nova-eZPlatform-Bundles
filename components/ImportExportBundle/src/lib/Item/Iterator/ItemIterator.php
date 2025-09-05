<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReaderIterator;

class ItemIterator extends AbstractReaderIterator
{
    protected \Iterator $innerIterator;
    protected ?IteratorItemTransformerInterface $itemTransformer = null;

    public function __construct(
        int $totalCount,
        \Iterator $innerIterator,
        ?IteratorItemTransformerInterface $itemTransformer = null
    ) {
        $this->itemTransformer = $itemTransformer;
        $this->innerIterator = $innerIterator;
        parent::__construct($totalCount);
    }

    public function current()
    {
        $item = $this->innerIterator->current();
        if ($this->itemTransformer instanceof IteratorItemTransformerInterface) {
            return ($this->itemTransformer)($item);
        }

        return $item;
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
