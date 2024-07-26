<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\SortClauseHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause as APISortClause;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler;
use Novactive\EzSolrSearchExtra\Query\SortClause;

/**
 * Class CustomField.
 */
class CustomField extends SortClauseHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(APISortClause $sortClause): bool
    {
        return $sortClause instanceof SortClause\CustomField;
    }

    public function applySelect(QueryBuilder $query, APISortClause $sortClause, int $number): array
    {
        return [];
    }
}
