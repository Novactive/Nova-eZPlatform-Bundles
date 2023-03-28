<?php

/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\Services;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Novactive\EzRssFeedBundle\Entity\RssFeeds;
use Novactive\EzRssFeedBundle\Repository\Values\FeedValueObject;
use Symfony\Component\Routing\RouterInterface;

class RssFeedsService
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var RouterInterface
     */
    private $urlAliasRouter;

    public function __construct(Repository $repository, RouterInterface $urlAliasRouter)
    {
        $this->repository = $repository;
        $this->urlAliasRouter = $urlAliasRouter;
    }

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    public function getUrlAliasRouter(): RouterInterface
    {
        return $this->urlAliasRouter;
    }

    public function fetchContent(RssFeeds $rssFeed): array
    {
        $results = [];
        $locationService = $this->getRepository()->getLocationService();
        $searchService = $this->getRepository()->getSearchService();
        $numberObjects = $rssFeed->getNumberOfObject();
        $queryFilter = [];
        $mappingFieldIdentifier = [];
        $query = new Query();

        foreach ($rssFeed->getFeedItems()->toArray() as $filter) {
            $filter = $filter->toArray();
            $criterion = [];
            $mappingFieldIdentifier[$filter['contentTypeId']] = $filter['fieldTypesIdentifier'];

            if ($filter['includeSubtreePath']) {
                $criterion[] = new Query\Criterion\Subtree(
                    $locationService->loadLocation($filter['locationId'])->pathString
                );
            } else {
                $criterion[] = new Query\Criterion\LocationId(
                    $filter['locationId']
                );
            }

            $criterion[] = new Query\Criterion\ContentTypeId($filter['contentTypeId']);

            $queryFilter[] = new Query\Criterion\LogicalAnd($criterion);
        }

        $query->filter = new Query\Criterion\LogicalOr($queryFilter);
        $query->limit = $numberObjects;

        switch ($rssFeed->getSortType()) {
            case RssFeeds::SORT_TYPE_PUBLICATION:
                $query->sortClauses = [
                new Query\SortClause\DatePublished($rssFeed->getSortDirection()),
                ];

                break;

            case RssFeeds::SORT_TYPE_MODIFICATION:
                $query->sortClauses = [
                new Query\SortClause\DateModified($rssFeed->getSortDirection()),
                ];

                break;

            case RssFeeds::SORT_TYPE_NAME:
                $query->sortClauses = [
                new Query\SortClause\ContentName($rssFeed->getSortDirection()),
                ];

                break;
        }

        $searchResult = $searchService->findContent($query);

        if ($searchResult->totalCount > 0) {
            foreach ($searchResult->searchHits as $content) {
                $results[] = new FeedValueObject(
                    $content->valueObject,
                    $mappingFieldIdentifier[$content->valueObject->contentInfo->contentTypeId]
                );
            }
        }

        return $results;
    }
}
