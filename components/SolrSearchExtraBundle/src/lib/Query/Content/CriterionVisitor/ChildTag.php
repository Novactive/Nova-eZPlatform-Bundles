<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Solr\Query\Common\CriterionVisitor\CustomField\CustomFieldIn as BaseVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\ChildTag as ChildTagCriterion;

class ChildTag extends BaseVisitor
{
    public function canVisit(Criterion $criterion)
    {
        return $criterion instanceof ChildTagCriterion;
    }

    /**
     * @param ChildTagCriterion $criterion
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        $stringQuery = $subVisitor->visit($criterion->criterion);

        return '{!child of="'.$criterion->ofParameter.'"}'.$stringQuery;
    }
}
