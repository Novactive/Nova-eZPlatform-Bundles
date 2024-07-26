<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

/**
 * Class CustomField.
 */
class CustomField extends SortClause
{
    /**
     * Constructs a new CustomField SortClause.
     */
    public function __construct(string $fieldIdentifier, string $sortDirection = Query::SORT_ASC)
    {
        parent::__construct($fieldIdentifier, $sortDirection);
    }
}
