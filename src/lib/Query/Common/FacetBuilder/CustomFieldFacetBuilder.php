<?php
/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtra\Query\Common\FacetBuilder;

use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder;

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
}
