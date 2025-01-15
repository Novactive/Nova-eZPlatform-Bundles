<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use ArrayIterator;
use Doctrine\DBAL\Connection;
use Iterator;
use SeekableIterator;

class DoctrineSeekableItemIterator implements ReaderIteratorInterface, SeekableIterator
{
    public const DEFAULT_BATCH_SIZE = 25;
    protected Connection $connection;
    protected string $queryString;
    protected string $countQueryString;
    protected int $batchSize = self::DEFAULT_BATCH_SIZE;
    private ?Iterator $innerIterator;
    private int $position = 0;

    public function __construct(
        Connection $connection,
        string $queryString,
        string $countQueryString,
        int $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
        $this->batchSize = $batchSize;
        $this->countQueryString = $countQueryString;
        $this->queryString = $queryString;
        $this->connection = $connection;
    }

    private function fetch(): Iterator
    {
        $queryString = sprintf('%s LIMIT %d OFFSET %d', $this->queryString, $this->batchSize, $this->position);

        return new ArrayIterator($this->connection->executeQuery($queryString)->fetchAllAssociative());
    }

    private function initialize(): void
    {
        $this->position = 0;
        $this->innerIterator = $this->fetch();
    }

    private function isInitialized(): bool
    {
        return isset($this->innerIterator);
    }

    public function current()
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return $this->innerIterator->current();
    }

    public function next(): void
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }
        ++$this->position;
        $this->innerIterator->next();
        if (!$this->innerIterator->valid() && ($this->position % $this->batchSize) === 0) {
            $this->innerIterator = $this->fetch();
        }
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return $this->innerIterator->valid();
    }

    public function rewind(): void
    {
        $this->initialize();
    }

    public function count(): int
    {
        return $this->connection->executeQuery($this->countQueryString)->fetchOne();
    }

    public function seek($offset): void
    {
        $this->position = $offset;
        $this->innerIterator = $this->fetch();
    }
}
