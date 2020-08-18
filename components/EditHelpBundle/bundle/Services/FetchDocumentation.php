<?php

namespace Novactive\Bundle\NovaeZEditHelpBundle\Services;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\SignalSlot\Repository;

class FetchDocumentation
{
    public const TOOLTIP_CONTENT_TYPE = 'nova_help_tooltip';

    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getByContentType(ContentType $contentType): ?SearchHit
    {
        $query = new Query(
            [
                'filter' => new LogicalAnd(
                    [
                        new Criterion\ContentTypeIdentifier([self::TOOLTIP_CONTENT_TYPE]),
                        new Field('identifier', '=', $contentType->identifier),
                    ]
                ),
            ]
        );

        $searchService = $this->repository->getSearchService();
        $searchResult = $searchService->findContent($query);
        // Content is found
        if ($searchResult->totalCount > 0) {
            $documentation = $searchResult->searchHits[0];
        }

        return $documentation ?? null;
    }

    public function getChildrenByLocationId(int $locationId): ?array
    {
        $query = new LocationQuery(
            [
                'filter' => new LogicalAnd(
                    [
                        new ParentLocationId($locationId),
                    ]
                ),
            ]
        );

        $searchService = $this->repository->getSearchService();
        $searchResult = $searchService->findContent($query);
        // Content is found
        if ($searchResult->totalCount > 0) {
            $children = $searchResult->searchHits;
        }

        return $children ?? null;
    }
}
