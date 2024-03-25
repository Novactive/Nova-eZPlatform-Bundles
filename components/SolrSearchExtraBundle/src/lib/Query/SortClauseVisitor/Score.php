<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause as APISortClause;
use Ibexa\Contracts\Solr\Query\SortClauseVisitor;
use Novactive\EzSolrSearchExtra\Query\SortClause;

/**
 * Class Score.
 */
class Score extends SortClauseVisitor
{
    /**
     * Check if visitor is applicable to current sortClause.
     */
    public function canVisit(APISortClause $sortClause): bool
    {
        return $sortClause instanceof SortClause\Score;
    }

    /**
     * Map field value to a proper Solr representation.
     */
    public function visit(APISortClause $sortClause): string
    {
        return 'score'.$this->getDirection($sortClause);
    }
}
