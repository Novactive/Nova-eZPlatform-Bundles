<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\AggregationResult;

use ArrayIterator;
use Countable;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Iterator;
use IteratorAggregate;

class RawTermAggregationResult extends AggregationResult implements IteratorAggregate, Countable
{
    /** @var RawTermAggregationResultEntry[] */
    private $entries;

    public function __construct(string $name, iterable $entries = [])
    {
        parent::__construct($name);

        $this->entries = $entries;
    }

    /**
     * @return RawTermAggregationResultEntry[]
     */
    public function getEntries(): iterable
    {
        return $this->entries;
    }

    /**
     * @param object|string|int $key
     */
    public function getEntry($key): ?RawTermAggregationResultEntry
    {
        foreach ($this->entries as $entry) {
            if ($entry->getKey() == $key) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * @param object|string|int $key
     */
    public function hasEntry($key): bool
    {
        return null !== $this->getEntry($key);
    }

    public function count(): int
    {
        return count($this->entries);
    }

    public function getIterator(): Iterator
    {
        if (empty($this->entries)) {
            return new ArrayIterator();
        }

        foreach ($this->entries as $entry) {
            yield $entry->getKey() => $entry->getCount();
        }
    }

    public static function createForAggregation(Aggregation $aggregation, iterable $entries = []): self
    {
        return new self($aggregation->getName(), $entries);
    }
}
