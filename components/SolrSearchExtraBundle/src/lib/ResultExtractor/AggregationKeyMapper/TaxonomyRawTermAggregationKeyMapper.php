<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor\AggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;

final class TaxonomyRawTermAggregationKeyMapper extends AbstractRawTermAggregationKeyMapper
{
    public function __construct(
        private TaxonomyServiceInterface $taxonomyService
    ) {
    }

    public function mapKey(Aggregation $aggregation, $key): array
    {
        $taxonomyEntry = $this->taxonomyService->loadEntryById((int) $key);

        return [
            'name' => $taxonomyEntry->name,
            'identifier' => $taxonomyEntry->identifier,
        ];
    }
}
