<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReaderIterator;
use Iterator;

/**
 * @template TKey
 * @template-covariant TValue
 * @template TInnerIterator of Iterator
 * @template TItemTransformer of IteratorItemTransformerInterface
 * @extends AbstractReaderIterator<TKey, TValue>
 */
class ItemIterator extends AbstractReaderIterator
{
    /**
     * @param TInnerIterator        $innerIterator
     * @param TItemTransformer|null $itemTransformer
     */
    public function __construct(
        int $totalCount,
        protected Iterator $innerIterator,
        protected ?IteratorItemTransformerInterface $itemTransformer = null
    ) {
        parent::__construct($totalCount);
    }

    /**
     * @return TValue
     */
    public function current(): mixed
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

    /**
     * @return TKey
     */
    public function key(): mixed
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
