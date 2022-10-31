<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ResultExtractor\AggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;

final class TaxonomyRawTermAggregationKeyMapper extends AbstractRawTermAggregationKeyMapper
{
    private $taxonomyService;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService
    ) {
        $this->taxonomyService = $taxonomyService;
    }

    public function mapKey(Aggregation $aggregation, int $key): array
    {
        $taxonomyEntry = $this->taxonomyService->loadEntryById($key);

        return [
            'name' => $taxonomyEntry->name,
            'identifier' => $taxonomyEntry->identifier,
        ];
    }
}
