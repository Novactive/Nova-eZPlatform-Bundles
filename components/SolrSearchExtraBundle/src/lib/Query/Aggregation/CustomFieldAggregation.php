<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Aggregation;

class CustomFieldAggregation extends RawTermAggregation
{
    /**
     * @param array<string> $excludeTags
     * @param array<string> $excludeEntries
     */
    public function __construct(
        string $name,
        string $field,
        array $excludeTags = [],
        array $excludeEntries = []
    ) {
        parent::__construct($name, $field, $excludeTags);
    }
}
