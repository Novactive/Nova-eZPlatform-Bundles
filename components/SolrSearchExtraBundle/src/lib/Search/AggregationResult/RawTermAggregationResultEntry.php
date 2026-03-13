<?php

namespace Novactive\EzSolrSearchExtra\Search\AggregationResult;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class RawTermAggregationResultEntry extends ValueObject
{
    public function __construct(
        private readonly mixed $key,
        private readonly int $count,
        private readonly ?string $name = null,
        private readonly ?string $identifier = null,
        private readonly array $nestedResults = []
    ) {
        parent::__construct();
    }

    public function getKey(): mixed
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function hasNestedResults(): bool
    {
        return !empty($this->nestedResults);
    }

    /**
     * @return array|\Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult[]
     */
    public function getNestedResults(): array
    {
        return $this->nestedResults;
    }
}
