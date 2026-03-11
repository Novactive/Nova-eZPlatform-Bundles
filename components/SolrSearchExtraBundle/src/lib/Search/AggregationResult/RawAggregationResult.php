<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\AggregationResult;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use stdClass;

class RawAggregationResult extends AggregationResult
{
    public function __construct(string $name, protected stdClass $value)
    {
        parent::__construct($name);
    }

    public function getValue(): stdClass
    {
        return $this->value;
    }
}
