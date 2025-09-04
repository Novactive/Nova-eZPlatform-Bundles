<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv;

use AlmaviaCX\Bundle\IbexaImportExport\File\FileReadIterator;

/**
 * @extends FileReadIterator<array<int, string>|false>
 */
class CsvFileReaderIterator extends FileReadIterator
{
    public function __construct(
        $stream,
        int $firstLineNumber = 0,
        protected string $delimiter = ',',
        protected string $enclosure = '"',
        protected string $escape = '\\'
    ) {
        parent::__construct($stream, $firstLineNumber);
    }

    protected function getLine()
    {
        return fgetcsv(
            $this->stream,
            null,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );
    }
}
