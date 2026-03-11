<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\LocationDistance;

class LocationDistanceRange extends CriterionVisitor
{
    private const int MAX_EARTH_DISTANCE_KM = 63510;

    public function __construct(
        private string $solrVersion
    ) {
    }

    public function canVisit(CriterionInterface $criterion): bool
    {
        return
            $criterion instanceof LocationDistance &&
            (Operator::LT === $criterion->operator ||
              Operator::LTE === $criterion->operator ||
              Operator::GT === $criterion->operator ||
              Operator::GTE === $criterion->operator ||
              Operator::BETWEEN === $criterion->operator);
    }

    /**
     * @param LocationDistance $criterion
     */
    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        if (!$this->isSolrInMaxVersion('9.3.0')) {
            return $this->visitForSolr9($criterion);
        }

        $criterion->value = (array) $criterion->value;

        $start = $criterion->value[0];
        $end = $criterion->value[1] ?? self::MAX_EARTH_DISTANCE_KM;

        if (
            (Operator::LT === $criterion->operator) ||
             (Operator::LTE === $criterion->operator)
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

    /**
     * @param LocationDistance $criterion
     */
    private function visitForSolr9(CriterionInterface $criterion): string
    {
        if (is_array($criterion->value)) {
            $minDistance = $criterion->value[0];
            $maxDistance = $criterion->value[1] ?? self::MAX_EARTH_DISTANCE_KM;
        } else {
            $minDistance = 0;
            $maxDistance = $criterion->value;
        }

        $sign = '';
        if (
            (Operator::GT === $criterion->operator) ||
            (Operator::GTE === $criterion->operator)
        ) {
            $sign = '-';
        }

        /** @var Criterion\Value\MapLocationValue $location */
        $location = $criterion->valueData;

        if (Operator::BETWEEN === $criterion->operator) {
            $query = sprintf(
                '{!geofilt sfield=%s pt=%F,%F d=%s} AND -{!geofilt sfield=%s pt=%F,%F d=%s}',
                $criterion->target,
                $location->latitude,
                $location->longitude,
                $maxDistance,
                $criterion->target,
                $location->latitude,
                $location->longitude,
                $minDistance
            );
        } else {
            $query = sprintf(
                '%s{!geofilt sfield=%s pt=%F,%F d=%s}',
                $sign,
                $criterion->target,
                $location->latitude,
                $location->longitude,
                $maxDistance
            );
        }

        return "{$query} AND {$criterion->target}:[* TO *]";
    }

    private function isSolrInMaxVersion(string $maxVersion): bool
    {
        return version_compare($this->solrVersion, $maxVersion, '<');
    }
}
