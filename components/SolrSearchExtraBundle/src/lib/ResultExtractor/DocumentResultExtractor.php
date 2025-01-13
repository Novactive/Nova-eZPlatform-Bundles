<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor;

use Ibexa\Solr\ResultExtractor;

class DocumentResultExtractor extends ResultExtractor
{
    public function extractHit($hit)
    {
        return $hit;
    }
}
