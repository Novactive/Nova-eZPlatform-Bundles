<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawAggregation;

class EnhancedRawTermAggregation extends AbstractTermAggregation implements RawAggregation
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
    public $excludeTags;

    public function __construct(
        string $name,
        string $fieldName
    ) {
        parent::__construct($name);

        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
