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

namespace Novactive\EzSolrSearchExtra\Query\SortClauseVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\SortClause as APISortClause;
use EzSystems\EzPlatformSolrSearchEngine\Query\SortClauseVisitor;
use Novactive\EzSolrSearchExtra\Query\SortClause;

/**
 * Class Score.
 */
class Score extends SortClauseVisitor
{
    /**
     * Check if visitor is applicable to current sortClause.
     *
     * @return bool
     */
    public function canVisit(APISortClause $sortClause)
    {
        return $sortClause instanceof SortClause\Score;
    }

    /**
     * Map field value to a proper Solr representation.
     *
     * @return string
     */
    public function visit(APISortClause $sortClause)
    {
        return 'score'.$this->getDirection($sortClause);
    }
}
