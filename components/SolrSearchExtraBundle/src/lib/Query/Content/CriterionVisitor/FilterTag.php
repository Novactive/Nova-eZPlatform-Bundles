<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Solr\Query\Common\CriterionVisitor\CustomField\CustomFieldIn as BaseVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\FilterTag as FiltertagCriterion;

class FilterTag extends BaseVisitor
{
    /**
     * Check if visitor is applicable to current criterion.
     */
    public function canVisit(Criterion $criterion): bool
    {
        return $criterion instanceof FiltertagCriterion;
    }

    /**
     * Map field value to a proper Solr representation.
     *
     * @param \Novactive\EzSolrSearchExtra\Query\Content\Criterion\FilterTag $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        $stringQuery = $subVisitor->visit($criterion->criterion);
        $stringQuery = trim($stringQuery, '()');

        return '{!tag='.$criterion->tag.'}('.$stringQuery.')';
    }
}
