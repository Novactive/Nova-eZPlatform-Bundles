<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\File;

use Countable;
use SeekableIterator;

/**
 * @template TValue
 * @implements SeekableIterator<int, TValue>
 */
class FileReadIterator implements SeekableIterator, Countable
{
    /** @var TValue */
    protected $line;
    protected int $lineNumber = 0;

    /**
     * @param resource $stream
     */
    public function __construct(
        protected $stream,
        protected int $firstLineNumber = 0
    ) {
        $this->lineNumber = $firstLineNumber;
    }

    /**
     * @return TValue
     */
    protected function getLine()
    {
        return fgets($this->stream);
    }

    /**
     * @param int $offset
     */
    public function seek($offset): void
    {
        fseek($this->stream, 0);
        if ($offset > 0) {
            for ($i = 0; $i < $offset; ++$i) {
                fgets($this->stream);
            }
        }
        $this->lineNumber = $offset;
        $this->line = $this->getLine();
    }

    public function rewind(): void
    {
        $this->seek($this->firstLineNumber);
    }

    public function valid(): bool
    {
        return false !== $this->line;
    }

    /**
     * @return TValue
     */
    public function current(): mixed
    {
        return $this->line;
    }

    public function key(): int
    {
        return $this->lineNumber;
    }

    public function next(): void
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

    public function count(): int
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
