<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor;
use Novactive\EzSolrSearchExtra\Query\Aggregation\RawAggregation;
use Novactive\EzSolrSearchExtra\Search\AggregationResult\RawAggregationResult;
use stdClass;

class RawAggregationResultExtractor implements AggregationResultExtractor
{
    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof RawAggregation;
    }

    public function extract(Aggregation $aggregation, array $languageFilter, stdClass $data): AggregationResult
    {
        return new RawAggregationResult(
            $aggregation->getName(),
            $data
        );
    }
}
