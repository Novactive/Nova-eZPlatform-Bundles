<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Pagerfanta\Adapter\AdapterInterface;

class FacetedContentSearchAdapter implements AdapterInterface
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Query
     */
    private $query;

    /**
     * @var \Ibexa\Contracts\Core\Repository\SearchService
     */
    private $searchService;

    /**
     * @var int
     */
    private $nbResults;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection
     */
    private $aggregations;

    public function __construct(Query $query, SearchService $searchService)
    {
        $this->query = $query;
        $this->searchService = $searchService;
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

        $list = [];
        foreach ($searchResult->searchHits as $hit) {
            $list[] = $hit->valueObject;
        }

        return $list;
    }
}
