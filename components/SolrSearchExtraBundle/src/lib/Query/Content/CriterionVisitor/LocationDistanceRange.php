<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\LocationDistance;

class LocationDistanceRange extends CriterionVisitor
{
    /**
     * Check if visitor is applicable to current criterion.
     *
     * @return bool
     */
    public function canVisit(Criterion $criterion)
    {
        return
            $criterion instanceof LocationDistance
            && (Operator::LT === $criterion->operator
              || Operator::LTE === $criterion->operator
              || Operator::GT === $criterion->operator
              || Operator::GTE === $criterion->operator
              || Operator::BETWEEN === $criterion->operator);
    }

    public function visit(Criterion $criterion, ?CriterionVisitor $subVisitor = null)
    {
        $criterion->value = (array) $criterion->value;

        $start = $criterion->value[0];
        $end = isset($criterion->value[1]) ? $criterion->value[1] : 63510;

        if (
            (Operator::LT === $criterion->operator)
             || (Operator::LTE === $criterion->operator)
        ) {
            $end = $start;
            $start = null;
        }

        /** @var Criterion\Value\MapLocationValue $location */
        $location = $criterion->valueData;

        $query = sprintf(
            '{!geofilt sfield=%s pt=%F,%F d=%s}',
            $criterion->target,
            $location->latitude,
            $location->longitude,
            $end
        );
        if (null !== $start) {
            $query = sprintf("{!frange l=%F}{$query}", $start);
        }

        return "({$query} AND {$criterion->target}_0_coordinate:[* TO *])";
    }
}
