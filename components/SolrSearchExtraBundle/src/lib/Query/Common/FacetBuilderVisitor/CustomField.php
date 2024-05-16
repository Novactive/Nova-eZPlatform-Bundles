<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Common\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder\FacetBuilderVisitor;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder\FacetFieldVisitor;
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
                'name' => $facetBuilder->name,
                'entries' => $this->mapData($data),
                'field' => $facetBuilder->field,
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
     * @param CustomFieldFacetBuilder $facetBuilder
     * @param string                  $fieldId
     *
     * @return array|string[]
     */
    public function visitBuilder(FacetBuilder $facetBuilder, $fieldId)
    {
        $excludeTags = ['dt'];
        if ($facetBuilder->excludeTags) {
            array_push($excludeTags, ...$facetBuilder->excludeTags);
        }

        $excludeTags = implode(',', $excludeTags);

        $facetParams = [
            'facet.field' => "{!ex={$excludeTags} key=${fieldId}}$facetBuilder->field",
            "f.{$facetBuilder->field}.facet.limit" => $facetBuilder->limit,
            "f.{$facetBuilder->field}.facet.mincount" => $facetBuilder->minCount,
        ];

        if (!empty($facetBuilder->excludeEntries)) {
            $facetParams["f.{$facetBuilder->field}.facet.excludeTerms"] = implode(',', $facetBuilder->excludeEntries);
        }

        return $facetParams;
    }
}
