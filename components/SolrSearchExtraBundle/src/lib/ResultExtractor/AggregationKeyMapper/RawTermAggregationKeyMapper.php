<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor\AggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

final class RawTermAggregationKeyMapper extends AbstractRawTermAggregationKeyMapper
{
    public function mapKey(Aggregation $aggregation, $key): array
    {
        return [
            'name' => $key,
            'identifier' => null,
        ];
    }
}
