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
    /** @var AbstractRawTermAggregationKeyMapper */
    private readonly RawTermAggregationKeyMapper|AbstractRawTermAggregationKeyMapper $keyMapper;

    public function __construct(
        private readonly string $aggregationClass,
        protected AggregationResultExtractor $aggregationResultExtractor,
        ?AbstractRawTermAggregationKeyMapper $keyMapper = null
    ) {
        if (null === $keyMapper) {
            $keyMapper = new RawTermAggregationKeyMapper();
        }

        $this->keyMapper = $keyMapper;
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
                    $nestedAggregationtsResults = [];
                    if (!empty($aggregation->nestedAggregations)) {
                        foreach ($aggregation->nestedAggregations as $nestedAggregationName => $nestedAggregation) {
                            if (is_string($nestedAggregation)) {
                                $results = $bucket->{$nestedAggregationName} ?? null;
                                $nestedAggregationtsResults[$nestedAggregationName] = $results;
                            } else {
                                $name = $nestedAggregation->getName();
                                if (isset($bucket->{$name})) {
                                    $nestedAggregationtsResults[$name] = $this->aggregationResultExtractor->extract(
                                        $nestedAggregation,
                                        $languageFilter,
                                        $bucket->{$name}
                                    );
                                }
                            }
                        }
                    }

                    $entries[] = new RawTermAggregationResultEntry(
                        $key,
                        $bucket->count,
                        $mappedKeys[$key]['name'] ?? null,
                        $mappedKeys[$key]['identifier'] ?? null,
                        $nestedAggregationtsResults
                    );
                }
            }
        }

        return new TermAggregationResult($aggregation->getName(), $entries);
    }

    /**
     * @return array<mixed>
     */
    private function getKeys(stdClass $data): array
    {
        $keys = [];
        foreach ($data->buckets as $bucket) {
            $keys[] = $bucket->val;
        }

        return $keys;
    }
}
