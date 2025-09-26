<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

class RawAggregation implements Aggregation
{
    public function __construct(
        protected string $name,
        protected array $value
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): array
    {
        return $this->value;
    }
}
