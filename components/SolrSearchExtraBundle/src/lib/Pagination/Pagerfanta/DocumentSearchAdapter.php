<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SpellcheckResult;
use Ibexa\Core\Pagination\Pagerfanta\SearchResultAdapter;
use Novactive\EzSolrSearchExtra\Repository\DocumentSearchServiceInterface;
use Pagerfanta\Adapter\AdapterInterface;

class DocumentSearchAdapter implements AdapterInterface, SearchResultAdapter
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection|null */
    private $aggregations;

    /** @var int|null */
    private $totalCount;

    /** @var float|null */
    private $time;

    /** @var bool|null */
    private $timedOut;

    /** @var float|null */
    private $maxScore;

    public function __construct(
        protected Query $query,
        protected DocumentSearchServiceInterface $documentSearchService,
        protected array $languageFilter = []
    ) {
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     */
    public function getNbResults()
    {
        if (isset($this->totalCount)) {
            return $this->totalCount;
        }

        $countQuery = clone $this->query;
        $countQuery->limit = 0;
        // Skip facets/aggregations & spellcheck computing
        $countQuery->facetBuilders = [];
        $countQuery->aggregations = [];
        $countQuery->spellcheck = null;

        $searchResults = $this->executeQuery(
            $this->documentSearchService,
            $countQuery,
            $this->languageFilter
        );

        return $this->totalCount = $searchResults->totalCount;
    }

    /**
     * Returns a slice of the results, as SearchHit objects.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit[]
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $searchResult = $this->executeQuery(
            $this->documentSearchService,
            $query,
            $this->languageFilter
        );

        $this->aggregations = $searchResult->getAggregations();
        $this->time = $searchResult->time;
        $this->timedOut = $searchResult->timedOut;
        $this->maxScore = $searchResult->maxScore;
        $this->spellcheck = $searchResult->getSpellcheck();

        // Set count for further use if returned by search engine despite !performCount (Solr, ES)
        if (!isset($this->totalCount) && isset($searchResult->totalCount)) {
            $this->totalCount = $searchResult->totalCount;
        }

        return $searchResult->searchHits;
    }

    public function getAggregations(): AggregationResultCollection
    {
        if (null === $this->aggregations) {
            $aggregationQuery = clone $this->query;
            $aggregationQuery->offset = 0;
            $aggregationQuery->limit = 0;
            $aggregationQuery->spellcheck = null;

            $searchResults = $this->executeQuery(
                $this->documentSearchService,
                $aggregationQuery,
                $this->languageFilter
            );

            $this->aggregations = $searchResults->aggregations;
        }

        return $this->aggregations;
    }

    public function getSpellcheck(): ?SpellcheckResult
    {
        if (null === $this->spellcheck) {
            $spellcheckQuery = clone $this->query;
            $spellcheckQuery->offset = 0;
            $spellcheckQuery->limit = 0;
            $spellcheckQuery->aggregations = [];

            $searchResults = $this->executeQuery(
                $this->documentSearchService,
                $spellcheckQuery,
                $this->languageFilter
            );

            $this->spellcheck = $searchResults->spellcheck;
        }

        return $this->spellcheck;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function getTimedOut(): ?bool
    {
        return $this->timedOut;
    }

    public function getMaxScore(): ?float
    {
        return $this->maxScore;
    }

    protected function executeQuery(
        DocumentSearchServiceInterface $documentSearchService,
        Query $query,
        array $languageFilter = []
    ) {
        return $documentSearchService->findDocument(
            $query,
            $languageFilter
        );
    }
}
