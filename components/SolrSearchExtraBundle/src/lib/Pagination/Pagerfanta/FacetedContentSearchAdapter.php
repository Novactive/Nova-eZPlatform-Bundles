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

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\Facet;
use Pagerfanta\Adapter\AdapterInterface;

class FacetedContentSearchAdapter implements AdapterInterface
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Query */
    private $query;

    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var int */
    private $nbResults;

    /** @var Facet[] */
    private $facets;

    public function __construct(Query $query, SearchService $searchService)
    {
        $this->query         = $query;
        $this->searchService = $searchService;
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     */
    public function getNbResults()
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $countQuery        = clone $this->query;
        $countQuery->limit = 0;

        return $this->nbResults = $this->searchService->findContent($countQuery)->totalCount;
    }

    /**
     * Return search facets.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return Facet[]
     */
    public function getFacets(): array
    {
        if (isset($this->facets)) {
            return $this->facets;
        }

        $facetQuery        = clone $this->query;
        $facetQuery->limit = 0;

        return $this->facets = $this->searchService->findContent($facetQuery)->facets;
    }

    /**
     * Returns a slice of the results, as SearchHit objects.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[]
     */
    public function getSlice($offset, $length)
    {
        $query               = clone $this->query;
        $query->offset       = $offset;
        $query->limit        = $length;
        $query->performCount = false;

        $searchResult = $this->searchService->findContent($query);

        if (!isset($this->nbResults) && isset($searchResult->totalCount)) {
            $this->nbResults = $searchResult->totalCount;
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
