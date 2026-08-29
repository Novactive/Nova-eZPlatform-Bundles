<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use SeekableIterator;

/**
 * @template-covariant TValue
 * @template TInnerIterator of SeekableIterator
 * @template TItemTransformer of IteratorItemTransformerInterface
 * @extends ItemIterator<int, TValue, TInnerIterator, TItemTransformer>
 * @implements SeekableIterator<int, TValue>
 */
class SeekableItemIterator extends ItemIterator implements SeekableIterator
{
    public function seek(int $offset): void
    {
        $this->innerIterator->seek($offset);
    }
}
