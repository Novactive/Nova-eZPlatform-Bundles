<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\Query\AggregationVisitor;
use Novactive\EzSolrSearchExtra\Query\Aggregation\EnhancedRawTermAggregation;

class EnhancedRawTermAggregationVisitor implements AggregationVisitor
{
    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof EnhancedRawTermAggregation;
    }

    /**
     * @param EnhancedRawTermAggregation $aggregation
     *
     * @return array|string[]
     */
    public function visit(
        AggregationVisitor $dispatcherVisitor,
        Aggregation $aggregation,
        array $languageFilter
    ): array {
        $facetInfos = [
            'type' => 'terms',
            'field' => $aggregation->getFieldName(),
            'limit' => $aggregation->getLimit(),
            'mincount' => $aggregation->getMinCount(),
        ];
        if (!empty($aggregation->excludeTags)) {
            $facetInfos['domain']['excludeTags'] = implode(',', $aggregation->excludeTags);
        }

        return $facetInfos;
    }
}
