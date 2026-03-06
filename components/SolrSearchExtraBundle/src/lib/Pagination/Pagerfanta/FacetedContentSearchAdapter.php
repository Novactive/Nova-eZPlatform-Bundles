<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Pagerfanta\Adapter\AdapterInterface;

class FacetedContentSearchAdapter implements AdapterInterface
{
    private Query $query;

    private SearchService $searchService;

    private ?int $nbResults = null;

    private ?AggregationResultCollection $aggregations = null;

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
     * Return search aggregations.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getAggregations(): AggregationResultCollection
    {
        if (isset($this->aggregations)) {
            return $this->aggregations;
        }

        $aggregationQuery = clone $this->query;
        $aggregationQuery->limit = 0;

        return $this->aggregations = $this->searchService->findContent($aggregationQuery)->aggregations;
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
