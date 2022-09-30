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

    public function __construct($key, int $count, ?string $name = null, string $identifier = null)
    {
        parent::__construct();

        $this->key = $key;
        $this->count = $count;
        $this->name = $name;
        $this->identifier = $identifier;
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
