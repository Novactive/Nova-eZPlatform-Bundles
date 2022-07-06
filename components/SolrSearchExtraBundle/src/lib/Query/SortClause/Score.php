<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

/**
 * Class Score.
 */
class Score extends SortClause
{
    /**
     * Constructs a new Score SortClause.
     */
    public function __construct(string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct('score', $sortDirection);
    }
}
