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

namespace Novactive\EzSolrSearchExtra\Query\SortClauseHandler;

use eZ\Publish\API\Repository\Values\Content\Query\SortClause as APISortClause;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler;
use Novactive\EzSolrSearchExtra\Query\SortClause;

/**
 * Class Score.
 */
class Score extends SortClauseHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(APISortClause $sortClause)
    {
        return $sortClause instanceof SortClause\Score;
    }

    /**
     * {@inheritdoc}
     */
    public function applySelect(SelectQuery $query, APISortClause $sortClause, $number)
    {
        return null;
    }
}
