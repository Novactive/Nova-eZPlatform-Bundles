<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawAggregation;

class RawTermAggregation extends AbstractTermAggregation implements RawAggregation
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @see https://solr.apache.org/guide/7_7/json-facet-api.html#filter-exclusions
     *
     * @var string[]
     */
    public array $excludeTags;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[]
     */
    public array $nestedAggregations;

    public function __construct(
        string $name,
        string $fieldName,
        ?array $excludeTags = [],
        ?array $nestedAggregations = []
    ) {
        parent::__construct($name);

        $this->fieldName = $fieldName;
        $this->excludeTags = $excludeTags;
        $this->nestedAggregations = $nestedAggregations;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setNestedAggregations(array $nestedAggregations): void
    {
        $this->nestedAggregations = $nestedAggregations;
    }
}
