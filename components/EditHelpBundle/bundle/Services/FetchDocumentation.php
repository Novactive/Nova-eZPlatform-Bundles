<?php

/**
 * NovaeZEditHelpBundle.
 *
 * @package   Novactive\Bundle\NovaeZEditHelpBundle
 *
 * @author    sergmike
 * @copyright 2019
 * @license   https://github.com/Novactive/NovaeZEditHelpBundle MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\NovaeZEditHelpBundle\Services;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\SiteAccessAware\Repository;

class FetchDocumentation
{
    public const TOOLTIP_CONTENT_TYPE = 'nova_help_tooltip';

    public function __construct(
        protected Repository $repository
    ) {

    }

    public function getByContentType(ContentType $contentType): ?Content
    {
        $query = new Query(
            [
                'filter' => new Query\Criterion\LogicalAnd(
                    [
                        new Query\Criterion\ContentTypeIdentifier([self::TOOLTIP_CONTENT_TYPE]),
                        new Query\Criterion\Field('identifier', '=', $contentType->identifier),
                    ]
                ),
            ]
        );

        $searchService = $this->repository->getSearchService();
        $searchResult = $searchService->findContent($query);
        // Content is found
        if ($searchResult->totalCount > 0) {
            $hit = $searchResult->searchHits[0];
            $valueObject = $hit->valueObject;
            if ($valueObject instanceof Content) {
                return $valueObject;
            }
        }

        return null;
    }

    /**
     * @param int $locationId
     * @return SearchHit[] de Content.
     * @throws InvalidArgumentException
     * @throws InvalidCriterionArgumentException
     */
    public function getChildrenByLocationId(int $locationId): array
    {
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LogicalAnd(
                    [
                        new Query\Criterion\ParentLocationId($locationId),
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

        return $children ?? [];
    }
}
