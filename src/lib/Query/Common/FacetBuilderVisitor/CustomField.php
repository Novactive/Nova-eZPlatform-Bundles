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

namespace Novactive\EzSolrSearchExtra\Query\Common\FacetBuilderVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder;
use EzSystems\EzPlatformSolrSearchEngine\Query\FacetBuilderVisitor;
use EzSystems\EzPlatformSolrSearchEngine\Query\FacetFieldVisitor;
use Novactive\EzSolrSearchExtra\Query\Common\FacetBuilder\CustomFieldFacetBuilder;
use Novactive\EzSolrSearchExtra\Values\Content\Search\Facet\CustomFieldFacet;

class CustomField extends FacetBuilderVisitor implements FacetFieldVisitor
{
    /**
     * {@inheritdoc}.
     */
    public function mapField($field, array $data, FacetBuilder $facetBuilder)
    {
        return new CustomFieldFacet(
            [
                'name'    => $facetBuilder->name,
                'entries' => $this->mapData($data),
                'field'   => $facetBuilder->field,
            ]
        );
    }

    /**
     * {@inheritdoc}.
     */
    public function canVisit(FacetBuilder $facetBuilder)
    {
        return $facetBuilder instanceof CustomFieldFacetBuilder;
    }

    /**
     * {@inheritdoc}.
     */
    public function visitBuilder(FacetBuilder $facetBuilder, $fieldId)
    {
        return [
            'facet.field'                             => "{!ex=dt key=${fieldId}}$facetBuilder->field",
            "f.{$facetBuilder->field}.facet.limit"    => $facetBuilder->limit,
            "f.{$facetBuilder->field}.facet.mincount" => $facetBuilder->minCount,
        ];
    }
}
