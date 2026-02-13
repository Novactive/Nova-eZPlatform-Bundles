<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\Query\AggregationVisitor;
use Novactive\EzSolrSearchExtra\Query\Aggregation\RawAggregation;

class RawAggregationVisitor implements AggregationVisitor
{
    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof RawAggregation;
    }

    /**
     * @param RawAggregation $aggregation
     */
    public function visit(
        AggregationVisitor $dispatcherVisitor,
        Aggregation $aggregation,
        array $languageFilter
    ): array {
        return $aggregation->getValue();
    }
}
