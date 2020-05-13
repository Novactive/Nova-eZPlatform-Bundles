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

namespace Novactive\EzSolrSearchExtra\Values\Content\Search\Facet;

use eZ\Publish\API\Repository\Values\Content\Search\Facet;

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
