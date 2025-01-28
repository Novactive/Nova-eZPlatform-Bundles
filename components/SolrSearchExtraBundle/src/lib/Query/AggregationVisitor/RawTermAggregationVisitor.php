<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\Query\AggregationVisitor;
use Novactive\EzSolrSearchExtra\Query\Aggregation\RawTermAggregation;

class RawTermAggregationVisitor implements AggregationVisitor
{
    private AggregationVisitor $aggregationVisitor;

    public function __construct(
        AggregationVisitor $aggregationVisitor
    ) {
        $this->aggregationVisitor = $aggregationVisitor;
    }

    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof RawTermAggregation;
    }

    /**
     * @param RawTermAggregation $aggregation
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
        if (!empty($aggregation->nestedAggregations)) {
            foreach ($aggregation->nestedAggregations as $aggregation) {
                if ($this->aggregationVisitor->canVisit($aggregation, $languageFilter)) {
                    $facetInfos['facet'][$aggregation->getName()] = $this->aggregationVisitor->visit(
                        $this->aggregationVisitor,
                        $aggregation,
                        $languageFilter
                    );
                }
            }
        }

        return $facetInfos;
    }
}
