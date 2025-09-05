<?php

namespace Novactive\Bundle\eZProtectedContentBundle\Services;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\SearchService;

class ReindexHelper
{
    public function __construct(
        protected readonly SearchService $searchService,
        protected readonly Repository $repository,
        protected readonly SearchHandler $searchHandler,
        protected readonly PersistenceHandler $persistenceHandler,
    ) {
    }

    /**
     * @param Content $content
     * @return void
     */
    public function reindexContent(Content $content): void
    {
        $contentId = $content->id;
        $contentVersionNo = $content->getVersionInfo()->versionNo;

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($contentId, $contentVersionNo)
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent($contentId);
        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    public function reindexChildren(Content $content, int $limit = 100): void
    {
        $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
        $pathStringArray = [];
        foreach ($locations as $location) {
            /** @var Location $location */
            $pathStringArray[] = $location->pathString;
        }

        if ($pathStringArray) {
            $query = new Query();
            $query->limit = $limit;
            $query->filter = new Query\Criterion\LogicalAnd([
                new Query\Criterion\Subtree($pathStringArray)
            ]);
            $query->sortClauses = [
                new Query\SortClause\ContentId(),
                // new Query\SortClause\Visibility(), // domage..
            ];
            $searchResult = $this->repository->getSearchService()->findContent($query);
            foreach ($searchResult->searchHits as $hit) {
                $this->reindexContent($hit->valueObject);
            }
        }
    }
}
