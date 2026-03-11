<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Spellcheck;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Solr\ResultExtractor;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchResult;
use Novactive\EzSolrSearchExtra\Values\DocumentHit;
use stdClass;

class DocumentResultExtractor extends ResultExtractor
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation[]           $aggregations
     * @param array{languages?: string[], languageCode?: string, useAlwaysAvailable?: bool} $languageFilter
     *
     * @return ExtendedSearchResult<DocumentHit, ValueObject>
     */
    public function extract(
        stdClass $data,
        array $aggregations = [],
        array $languageFilter = [],
        ?Spellcheck $spellcheck = null
    ) {
        $result = parent::extract(
            $data,
            $aggregations,
            $languageFilter,
            $spellcheck
        );

        $properties = [
            'aggregations' => $result->aggregations,
            'searchHits' => $result->searchHits,
            'spellcheck' => $result->spellcheck,
            'time' => $result->time,
            'timedOut' => $result->timedOut,
            'maxScore' => $result->maxScore,
            'totalCount' => $result->totalCount,
        ];

        if (isset($data->expanded)) {
            $properties['expanded'] = [];
            foreach ($data->expanded as $key => $expanded) {
                $expandedResult = new SearchResult(
                    [
                        'time' => $data->responseHeader->QTime / 1000,
                        'maxScore' => $expanded->maxScore,
                        'totalCount' => $expanded->numFound,
                    ]
                );

                foreach ($expanded->docs as $doc) {
                    $expandedResult->searchHits[] = $this->extractSearchHit($doc);
                }

                $properties['expanded'][$key] = $expandedResult;
            }
        }

        return new ExtendedSearchResult(
            $properties
        );
    }

    /**
     * @return SearchHit<DocumentHit>
     */
    protected function extractSearchHit(stdClass $doc): SearchHit
    {
        return new SearchHit(
            [
                'score' => $doc->score,
                'index' => $this->getIndexIdentifier($doc),
                'valueObject' => $this->extractHit($doc),
            ]
        );
    }

    public function extractHit(stdClass $hit): ValueObject
    {
        return new DocumentHit($hit);
    }
}
