<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawAggregation;

class RawTermAggregation extends AbstractTermAggregation implements RawAggregation
{
    /**
     * https://solr.apache.org/guide/7_7/json-facet-api.html#filter-exclusions.
     *
     * @param array<string>                     $excludeTags
     * @param array<string>                     $domain
     * @param array<string, Aggregation|string> $nestedAggregations
     */
    public function __construct(
        string $name,
        private string $fieldName,
        public array $excludeTags = [],
        public ?string $sort = null,
        public array $domain = [],
        public array $nestedAggregations = []
    ) {
        parent::__construct($name);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @param array<string, Aggregation|string> $nestedAggregations
     */
    public function setNestedAggregations(array $nestedAggregations): void
    {
        $this->nestedAggregations = $nestedAggregations;
    }

    /**
     * @return string[]
     */
    public function getDomain(): array
    {
        return $this->domain;
    }
}
