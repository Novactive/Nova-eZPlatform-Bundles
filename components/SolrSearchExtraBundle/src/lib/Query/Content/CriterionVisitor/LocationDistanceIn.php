<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\LocationDistance;

class LocationDistanceIn extends CriterionVisitor
{
    public function canVisit(Criterion $criterion)
    {
        return
            $criterion instanceof LocationDistance
            && (($criterion->operator ?: Operator::IN) === Operator::IN
                 || Operator::EQ === $criterion->operator);
    }

    public function visit(Criterion $criterion, ?CriterionVisitor $subVisitor = null)
    {
        /** @var Criterion\Value\MapLocationValue $location */
        $location = $criterion->valueData;
        $criterion->value = (array) $criterion->value;

        $queries = [];
        foreach ($criterion->value as $value) {
            $queries[] = sprintf(
                'geodist(%s,%F,%F):%s',
                $criterion->target,
                $location->latitude,
                $location->longitude,
                $value
            );
        }

        return '('.implode(' OR ', $queries).')';
    }
}
