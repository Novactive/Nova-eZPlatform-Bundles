<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Values\Content\Search\Facet;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

/**
 * @deprecated
 */
class CustomFieldFacet extends Facet
{
    /**
     * An array with value as key and count of matching content objects as value.
     *
     * @var array
     */
    public $entries;

    /**
     * @var string
     */
    public $field;
}
