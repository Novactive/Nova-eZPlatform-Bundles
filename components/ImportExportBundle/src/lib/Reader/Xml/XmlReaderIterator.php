<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xml;

use Countable;
use DOMDocument;
use SeekableIterator;

class XmlReaderIterator implements SeekableIterator, Countable
{
    /** @var \DOMNode|bool */
    protected $current;
    protected int $currentIndex = 0;
    protected XmlParser $xmlParser;

    /**
     * @param resource $stream
     */
    public function __construct($stream, string $nodeNameSelector)
    {
        $this->xmlParser = new XmlParser($stream, $nodeNameSelector);
    }

    public function current()
    {
        return $this->current;
    }

    public function next(): void
    {
        if (false !== $this->current) {
            $this->current = $this->findNextNode();
            ++$this->currentIndex;
        }
    }

    public function key()
    {
        return $this->currentIndex;
    }

    public function valid(): bool
    {
        return false !== $this->current;
    }

    public function rewind(): void
    {
        $this->xmlParser->rewind();
        $this->current = $this->findNextNode();
        $this->currentIndex = 0;
    }

    /**
     * @return \DOMNode|bool
     */
    protected function findNextNode()
    {
        $document = new DOMDocument();

        $xml = $this->xmlParser->parse();
        $fragment = $document->createDocumentFragment();
        $fragment->appendXML(trim($xml));
        $document->append($fragment);
        dump('Usage: '.memory_get_usage() / 1024 / 1024 .'Mo');

        return $document->firstChild;
    }

    public function seek($offset): void
    {
        if ($offset > 0) {
            for ($i = 0; $i < $offset; ++$i) {
                $this->xmlParser->parse();
            }
        }
        $this->currentIndex = $offset;
        $this->current = $this->findNextNode();
    }

    public function count(): int
    {
        $totalCount = 0;
        while ($this->xmlParser->parse()) {
            ++$totalCount;
        }
        $this->xmlParser->rewind();

        return $totalCount;
    }
}
