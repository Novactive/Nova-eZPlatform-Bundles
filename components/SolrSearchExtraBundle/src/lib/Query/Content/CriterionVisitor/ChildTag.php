<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\ChildTag as ChildTagCriterion;

class ChildTag extends CriterionVisitor
{
    public function canVisit(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ChildTagCriterion;
    }

    /**
     * @param ChildTagCriterion $criterion
     */
    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $stringQuery = $subVisitor->visit($criterion->criterion);

        return '{!child of="'.$criterion->ofParameter.'"}'.$stringQuery;
    }
}
