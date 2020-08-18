<?php
/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use EzSystems\EzPlatformSolrSearchEngine\Query\Common\CriterionVisitor\CustomField\CustomFieldIn as BaseVisitor;
use EzSystems\EzPlatformSolrSearchEngine\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\FilterTag as FiltertagCriterion;

class FilterTag extends BaseVisitor
{
    /**
     * Check if visitor is applicable to current criterion.
     *
     * @return bool
     */
    public function canVisit(Criterion $criterion)
    {
        return $criterion instanceof FiltertagCriterion;
    }

    /**
     * Map field value to a proper Solr representation.
     *
     * @param FiltertagCriterion                                           $criterion
     * @param \EzSystems\EzPlatformSolrSearchEngine\Query\CriterionVisitor $subVisitor
     *
     * @return string
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        return '{!tag='.$criterion->tag.'}'.$subVisitor->visit($criterion->criterion);
    }
}
