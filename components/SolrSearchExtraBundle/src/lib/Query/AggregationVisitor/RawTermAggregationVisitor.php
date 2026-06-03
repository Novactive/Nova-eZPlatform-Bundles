<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\Query\AggregationVisitor;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Aggregation\RawTermAggregation;

class RawTermAggregationVisitor implements AggregationVisitor
{
    public function __construct(
        private readonly AggregationVisitor $aggregationVisitor,
        private readonly CriterionVisitor $criterionVisitor
    ) {
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
            'domain' => $aggregation->getDomain(),
        ];
        if ($aggregation->sort) {
            $facetInfos['sort'] = $aggregation->sort;
        }
        if (!empty($aggregation->excludeTags)) {
            $facetInfos['domain']['excludeTags'] = implode(',', $aggregation->excludeTags);
        }

        if (isset($facetInfos['domain']['filter'])) {
            $facetDomainFilters = [];
            foreach ($facetInfos['domain']['filter'] as $facetDomainFilter) {
                if (is_string($facetDomainFilter)) {
                    $facetDomainFilters[] = $facetDomainFilter;
                } else {
                    $facetDomainFilters[] = $this->criterionVisitor->visit($facetDomainFilter);
                }
            }
            $facetInfos['domain']['filter'] = $facetDomainFilters;
        }

        foreach ($aggregation->nestedAggregations as $nestedAggregationName => $aggregation) {
            if (is_string($aggregation)) {
                $facetInfos['facet'][$nestedAggregationName] = $aggregation;
            } elseif ($this->aggregationVisitor->canVisit($aggregation, $languageFilter)) {
                $facetInfos['facet'][$aggregation->getName()] = $this->aggregationVisitor->visit(
                    $this->aggregationVisitor,
                    $aggregation,
                    $languageFilter
                );
            }
        }

        if (empty($facetInfos['domain'])) {
            unset($facetInfos['domain']);
        }

        return $facetInfos;
    }
}
