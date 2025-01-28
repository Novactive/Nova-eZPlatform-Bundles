<?php

namespace Novactive\EzSolrSearchExtra\Search\AggregationResult;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class RawTermAggregationResultEntry extends ValueObject
{
    private $key;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $identifier;

    /** @var int */
    private $count;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult[] */
    private $nestedResults;

    public function __construct(
        $key,
        int $count,
        ?string $name = null,
        ?string $identifier = null,
        array $nestedResults = []
    ) {
        parent::__construct();

        $this->key = $key;
        $this->count = $count;
        $this->name = $name;
        $this->identifier = $identifier;
        $this->nestedResults = $nestedResults;
    }

    public function getKey()
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
