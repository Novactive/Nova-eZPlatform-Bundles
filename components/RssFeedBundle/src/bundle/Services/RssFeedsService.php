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

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Novactive\EzRssFeedBundle\Entity\RssFeeds;
use Novactive\EzRssFeedBundle\Repository\Values\FeedValueObject;
use Symfony\Component\Routing\RequestContext;

class RssFeedsService
{
    private $repository;

    private $urlAliasRouter;

    private $urlDecorator;

    public function __construct(Repository $repository, UrlAliasRouter $urlAliasRouter, RequestContext $request)
    {
        $this->repository = $repository;
        $this->urlAliasRouter = $urlAliasRouter;
        $this->request = $request;
    }

    public function getUrlDecorator()
    {
        return $this->urlDecorator;
    }

    public function setUrlDecorator($urlDecorator): void
    {
        $this->urlDecorator = $urlDecorator;
    }

    public function fetchContent(RssFeeds $rssFeed)
    {
        $results = [];
        $locationService = $this->getRepository()->getLocationService();
        $searchService = $this->getRepository()->getSearchService();
        $numberObjects = $rssFeed->getNumberOfObject();
        $queryFilter = [];
        $mappingFieldIdentifier = [];

        foreach ($rssFeed->getFeedItems()->toArray() as $filter) {
            $filter = $filter->toArray();
            $criterion = [];
            $query = new Query();
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

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    public function getUrlAliasRouter(): UrlAliasRouter
    {
        return $this->urlAliasRouter;
    }
}
