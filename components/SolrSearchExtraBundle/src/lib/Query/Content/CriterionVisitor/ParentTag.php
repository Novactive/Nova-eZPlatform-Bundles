<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\ParentTag as ParentTagCriterion;

class ParentTag extends CriterionVisitor
{
    public function canVisit(Criterion $criterion)
    {
        return $criterion instanceof ParentTagCriterion;
    }

    /**
     * @param ParentTagCriterion $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        $stringQuery = $subVisitor->visit($criterion->criterion);

        return '{!parent which="'.$criterion->whichParameter.'"}'.$stringQuery;
    }
}
