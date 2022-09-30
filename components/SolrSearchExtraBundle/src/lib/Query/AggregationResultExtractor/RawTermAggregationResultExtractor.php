<?php

namespace Novactive\EzSolrSearchExtra\Query\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;
use Ibexa\Solr\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\NullAggregationKeyMapper;
use Novactive\EzSolrSearchExtra\Search\AggregationResult\RawTermAggregationResultEntry;
use stdClass;

class RawTermAggregationResultExtractor implements AggregationResultExtractor
{
    /** @var \Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper */
    private $keyMapper;

    /** @var string */
    private $aggregationClass;

    public function __construct(string $aggregationClass, TermAggregationKeyMapper $keyMapper = null)
    {
        if (null === $keyMapper) {
            $keyMapper = new NullAggregationKeyMapper();
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
