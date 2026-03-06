<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

/**
 * Transition alias for the deprecated CustomFieldFacetBuilder.
 *
 * Use this class (or RawTermAggregation directly) with $query->aggregations
 * instead of the removed CustomFieldFacetBuilder with $query->facetBuilders.
 *
 * @see RawTermAggregation
 */
class CustomFieldAggregation extends RawTermAggregation
{
    public function __construct(
        string $name,
        string $field,
        ?array $excludeTags = [],
        ?array $excludeEntries = []
    ) {
        parent::__construct($name, $field, $excludeTags);
    }
}
