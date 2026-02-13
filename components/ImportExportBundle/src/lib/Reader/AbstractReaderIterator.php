<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

/**
 * @template TKey
 * @template-covariant TValue
 * @implements ReaderIteratorInterface<TKey, TValue>
 */
abstract class AbstractReaderIterator implements ReaderIteratorInterface
{
    public function __construct(
        protected int $totalCount = 0
    ) {
    }

    public function count(): int
    {
        return $this->totalCount;
    }
}
