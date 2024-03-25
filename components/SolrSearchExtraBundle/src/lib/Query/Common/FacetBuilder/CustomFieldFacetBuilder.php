<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Common\FacetBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

/**
 * @deprecated 4.0.0
 * @see \Novactive\EzSolrSearchExtra\Query\Aggregation\RawTermAggregation
 */
class CustomFieldFacetBuilder extends FacetBuilder
{
    /**
     * The field identifier.
     *
     * @var string
     */
    public $field;

    /** @var string[] */
    public $excludeTags;

    /** @var string[] */
    public $excludeEntries;
}
