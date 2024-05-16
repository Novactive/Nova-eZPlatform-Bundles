<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawAggregation;

class RawTermAggregation extends AbstractTermAggregation implements RawAggregation
{
    public function __construct(
        string $name,
        private string $fieldName,
        public ?array $excludeTags = []
    ) {
        parent::__construct($name);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
