<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use ArrayIterator;
use SeekableIterator;

/**
 * @implements ReaderIteratorInterface<int, ArrayAccessor>
 * @implements SeekableIterator<int, ArrayAccessor>
 */
abstract class PaginatedQueryIterator implements ReaderIteratorInterface, SeekableIterator
{
    public const DEFAULT_BATCH_SIZE = 25;
    /**
     * @var ArrayIterator<int, array<string, mixed>>|null
     */
    protected ?ArrayIterator $innerIterator = null;
    protected int $position = 0;

    public function __construct(
        protected string $queryString,
        protected int $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     *
     * @return ArrayIterator<int, array<string, mixed>>
     */
    protected function fetch(): ArrayIterator
    {
        $queryString = sprintf('%s LIMIT %d OFFSET %d', $this->queryString, $this->batchSize, $this->position);

        return new ArrayIterator($this->executeQuery($queryString));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    abstract protected function executeQuery(string $queryString): array;

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function initialize(): void
    {
        $this->position = 0;
        $this->innerIterator = $this->fetch();
    }

    protected function isInitialized(): bool
    {
        return isset($this->innerIterator);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     *
     * @return ArrayAccessor<string, mixed>
     */
    public function current(): ArrayAccessor
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return new ArrayAccessor(
            $this->innerIterator->current()
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
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

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function rewind(): void
    {
        $this->initialize();
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function seek(int $offset): void
    {
        $this->position = $offset;
        $this->innerIterator = $this->fetch();
    }
}
