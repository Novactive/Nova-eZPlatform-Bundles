<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv;

use AlmaviaCX\Bundle\IbexaImportExport\File\FileReadIterator;

class CsvFileReadIterator extends FileReadIterator
{
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escape = '\\';

    public function __construct(
        $stream,
        int $firstLineNumber = 0,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ) {
        $this->escape = $escape;
        $this->enclosure = $enclosure;
        $this->delimiter = $delimiter;
        parent::__construct($stream, $firstLineNumber);
    }

    /**
     * @return array|false|null
     */
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
