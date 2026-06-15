<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor\AggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

abstract class AbstractRawTermAggregationKeyMapper implements TermAggregationKeyMapper
{
    public function map(Aggregation $aggregation, array $languageFilter, array $keys): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->mapKey($aggregation, $key);
        }

        return $results;
    }

    /**
     * @return array{name: mixed, identifier: ?string}
     */
    abstract public function mapKey(Aggregation $aggregation, mixed $key): array;
}
