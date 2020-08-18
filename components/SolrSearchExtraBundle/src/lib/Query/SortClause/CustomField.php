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

namespace Novactive\EzSolrSearchExtra\Query\SortClause;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

/**
 * Class CustomField.
 */
class CustomField extends SortClause
{
    /**
     * Constructs a new CustomField SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct(string $fieldIdentifier, $sortDirection = Query::SORT_ASC)
    {
        parent::__construct($fieldIdentifier, $sortDirection);
    }
}
