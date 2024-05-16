<?php

namespace Novactive\EzSolrSearchExtra\Search\AggregationResult;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class RawTermAggregationResultEntry extends ValueObject
{
    public function __construct(
        private $key,
        private int $count,
        private ?string $name = null,
        private ?string $identifier = null)
    {
        parent::__construct();
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
}
