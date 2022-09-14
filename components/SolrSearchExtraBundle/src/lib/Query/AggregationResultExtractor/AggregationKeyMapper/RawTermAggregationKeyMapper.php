<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\AggregationResultExtractor\AggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;
use Novactive\EzSolrSearchExtra\Query\Aggregation\RawTermAggregation;

abstract class RawTermAggregationKeyMapper implements TermAggregationKeyMapper
{
    /**
     * @param RawTermAggregation $aggregation
     */
    public function map(Aggregation $aggregation, array $languageFilter, array $keys): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->mapKey($aggregation, $key);
        }

        return $results;
    }

    abstract public function mapKey(Aggregation $aggregation, int $key): ?array;
}
