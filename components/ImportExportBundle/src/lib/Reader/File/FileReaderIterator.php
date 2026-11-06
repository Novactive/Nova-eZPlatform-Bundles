<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\File;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\File\FileReadIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;

/**
 * @extends FileReadIterator<ArrayAccessor<string, mixed>|false>
 * @implements ReaderIteratorInterface<int, ArrayAccessor<string, mixed>|false>
 */
class FileReaderIterator extends FileReadIterator implements ReaderIteratorInterface
{
    public function current(): mixed
    {
        $item = parent::current();
        if (false === $item) {
            return $item;
        }

        return new ArrayAccessor([
            'line' => $item,
            'lineNumber' => $this->lineNumber,
        ]);
    }
}
