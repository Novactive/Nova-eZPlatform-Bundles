<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\File;

use Iterator;

class FileReadIterator implements Iterator
{
    /** @var resource */
    protected $stream;
    protected $line;
    protected int $lineNumber = 0;
    protected int $firstLineNumber = 0;

    /**
     * @param resource $stream
     */
    public function __construct($stream, int $firstLineNumber = 0)
    {
        $this->stream = $stream;
        $this->firstLineNumber = $firstLineNumber;
        $this->lineNumber = $firstLineNumber;
    }

    protected function getLine()
    {
        return fgets($this->stream);
    }

    public function setLineNumber(int $lineNumber)
    {
        fseek($this->stream, 0);
        if ($lineNumber > 0) {
            for ($i = 0; $i < $lineNumber; ++$i) {
                fgets($this->stream);
            }
        }
        $this->lineNumber = $lineNumber;
        $this->line = $this->getLine();
    }

    public function rewind()
    {
        $this->setLineNumber($this->firstLineNumber);
    }

    public function valid(): bool
    {
        return false !== $this->line;
    }

    public function current()
    {
        return $this->line;
    }

    public function key(): int
    {
        return $this->lineNumber;
    }

    public function next()
    {
        if (false !== $this->line) {
            $this->line = $this->getLine();
            ++$this->lineNumber;
        }
    }

    public function __destruct()
    {
        fclose($this->stream);
    }

    public function getTotalLines(): int
    {
        $lines = 0;
        fseek($this->stream, 0);
        while (!feof($this->stream)) {
            $lines += substr_count(fread($this->stream, 8192), "\n");
        }

        fseek($this->stream, $this->lineNumber);

        return $lines;
    }
}
