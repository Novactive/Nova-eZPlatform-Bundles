<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

class RawAggregation implements Aggregation
{
    protected array $value;
    protected string $name;

    public function __construct(
        string $name,
        array $value
    ) {
        $this->name = $name;
        $this->value = $value;
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
