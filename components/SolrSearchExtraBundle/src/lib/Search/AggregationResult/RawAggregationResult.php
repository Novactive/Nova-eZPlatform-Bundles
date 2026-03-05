<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\AggregationResult;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;

class RawAggregationResult extends AggregationResult
{
    protected $value;

    public function __construct(string $name, $value)
    {
        $this->value = $value;
        parent::__construct($name);
    }

    public function getValue()
    {
        return $this->value;
    }
}
