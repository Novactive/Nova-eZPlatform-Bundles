<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\File;

class FileReadIterator implements \SeekableIterator, \Countable
{
    /** @var resource */
    protected $stream;
    /** @var string|false */
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

    /**
     * @return string|false
     */
    protected function getLine()
    {
        return fgets($this->stream);
    }

    public function seek($offset)
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

    public function rewind()
    {
        $this->seek($this->firstLineNumber);
    }

    public function valid(): bool
    {
        return false !== $this->line;
    }

    /**
     * @return false|string
     */
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
