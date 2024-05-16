<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Pagerfanta\Adapter\AdapterInterface;

class FacetedContentSearchAdapter implements AdapterInterface
{
    /**
     * @var int
     */
    private $nbResults;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection
     */
    private $aggregations;

    /**
     * @var Facet[]
     *
     * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
     */
    private $facets;

    /**
     * @param Query $query
     * @param SearchService $searchService
     */
    public function __construct(
        private Query $query, 
        private SearchService $searchService
    ) {
    }

    /**
     * Returns the number of results.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return int the number of results
     */
    public function getNbResults(): int
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $countQuery = clone $this->query;
        $countQuery->limit = 0;

        return $this->nbResults = $this->searchService->findContent($countQuery)->totalCount;
    }

    /**
     * Return search facets.
     *
     *@throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getAggregations(): AggregationResultCollection
    {
        if (isset($this->aggregations)) {
            return $this->aggregations;
        }

        $facetQuery = clone $this->query;
        $facetQuery->limit = 0;

        return $this->aggregations = $this->searchService->findContent($facetQuery)->aggregations;
    }

    /**
     * Return search facets.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return Facet[]
     *
     * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
     */
    public function getFacets(): array
    {
        if (isset($this->facets)) {
            return $this->facets;
        }

        $facetQuery = clone $this->query;
        $facetQuery->limit = 0;

        return $this->facets = $this->searchService->findContent($facetQuery)->facets;
    }

    /**
     * Returns a slice of the results, as SearchHit objects.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit[]
     */
    public function getSlice($offset, $length): array
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $searchResult = $this->searchService->findContent($query);

        if (!isset($this->nbResults) && isset($searchResult->totalCount)) {
            $this->nbResults = $searchResult->totalCount;
        }

        if (!isset($this->aggregations) && isset($searchResult->aggregations)) {
            $this->aggregations = $searchResult->aggregations;
        }

        if (!isset($this->facets) && isset($searchResult->facets)) {
            $this->facets = $searchResult->facets;
        }

        $list = [];
        foreach ($searchResult->searchHits as $hit) {
            $list[] = $hit->valueObject;
        }

        return $list;
    }
}
