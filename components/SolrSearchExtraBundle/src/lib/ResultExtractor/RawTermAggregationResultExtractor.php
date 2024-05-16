<?php

namespace Novactive\EzSolrSearchExtra\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor;
use Novactive\EzSolrSearchExtra\ResultExtractor\AggregationKeyMapper\AbstractRawTermAggregationKeyMapper;
use Novactive\EzSolrSearchExtra\ResultExtractor\AggregationKeyMapper\RawTermAggregationKeyMapper;
use Novactive\EzSolrSearchExtra\Search\AggregationResult\RawTermAggregationResultEntry;
use stdClass;

class RawTermAggregationResultExtractor implements AggregationResultExtractor
{
    public function __construct(
        private string $aggregationClass, 
        private ?AbstractRawTermAggregationKeyMapper $keyMapper = null)
    {
        if (null === $keyMapper) {
            $keyMapper = new RawTermAggregationKeyMapper();
        }

        $this->keyMapper = $keyMapper;
        $this->aggregationClass = $aggregationClass;
    }

    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof $this->aggregationClass;
    }

    public function extract(Aggregation $aggregation, array $languageFilter, stdClass $data): AggregationResult
    {
        $entries = [];
        if (isset($data->buckets)) {
            $mappedKeys = $this->keyMapper->map(
                $aggregation,
                $languageFilter,
                $this->getKeys($data)
            );

            foreach ($data->buckets as $bucket) {
                $key = $bucket->val;
                if (isset($mappedKeys[$key])) {
                    $entries[] = new RawTermAggregationResultEntry(
                        $key,
                        $bucket->count,
                        ...$mappedKeys[$key]
                    );
                }
            }
        }

        return new TermAggregationResult($aggregation->getName(), $entries);
    }

    private function getKeys(stdClass $data): array
    {
        $keys = [];
        foreach ($data->buckets as $bucket) {
            $keys[] = $bucket->val;
        }

        return $keys;
    }
}
