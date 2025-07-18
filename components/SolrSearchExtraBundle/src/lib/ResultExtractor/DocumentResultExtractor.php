<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Solr\ResultExtractor;
use stdClass;

class DocumentResultExtractor extends ResultExtractor
{
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
