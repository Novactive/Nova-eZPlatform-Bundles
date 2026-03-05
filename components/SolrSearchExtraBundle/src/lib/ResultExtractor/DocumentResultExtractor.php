<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Spellcheck;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Solr\ResultExtractor;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchResult;
use stdClass;

class DocumentResultExtractor extends ResultExtractor
{
    public function extract(
        $data,
        array $facetBuilders = [],
        array $aggregations = [],
        array $languageFilter = [],
        ?Spellcheck $spellcheck = null
    ) {
        $result = parent::extract(
            $data,
            $facetBuilders,
            $aggregations,
            $languageFilter,
            $spellcheck
        );

        $properties = [
            'facets' => $result->facets,
            'aggregations' => $result->aggregations,
            'searchHits' => $result->searchHits,
            'spellSuggestion' => $result->spellSuggestion,
            'spellcheck' => $result->spellcheck,
            'time' => $result->time,
            'timedOut' => $result->timedOut,
            'maxScore' => $result->maxScore,
            'totalCount' => $result->totalCount,
        ];

        if (isset($data->expanded)) {
            $properties['expanded'] = [];
            foreach ($data->expanded as $key => $expanded) {
                $result = new SearchResult(
                    [
                        'time' => $data->responseHeader->QTime / 1000,
                        'maxScore' => $expanded->maxScore,
                        'totalCount' => $expanded->numFound,
                    ]
                );

                foreach ($expanded->docs as $doc) {
                    $result->searchHits[] = $this->extractSearchHit($doc, $languageFilter);
                }

                $properties['expanded'][$key] = $result;
            }
        }

        return new ExtendedSearchResult(
            $properties
        );
    }

    protected function extractSearchHit(stdClass $doc, array $languageFilter): SearchHit
    {
        return new SearchHit(
            [
                'score' => $doc->score,
                'index' => $this->getIndexIdentifier($doc),
                'valueObject' => $this->extractHit($doc),
            ]
        );
    }

    public function extractHit($hit)
    {
        return $hit;
    }
}
