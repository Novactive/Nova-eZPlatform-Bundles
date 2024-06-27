<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

abstract class AbstractReaderIterator implements ReaderIteratorInterface
{
    protected int $totalCount = 0;

    public function __construct(int $totalCount)
    {
        $this->totalCount = $totalCount;
    }

    public function count(): int
    {
        return $this->totalCount;
    }
}
