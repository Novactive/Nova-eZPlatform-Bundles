<?php

/**
 * Nova eZ Accelerator.
 *
 * @package   Novactive\Bundle\eZAccelerator
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZAccelerator/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAccelerator\Core;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Search\Handler;
use Novactive\Bundle\eZAccelerator\Message\Search\IndexContent;
use Novactive\Bundle\eZAccelerator\Message\Search\IndexLocation;
use Novactive\Bundle\eZAccelerator\Message\Search\PurgeIndex;
use Novactive\Bundle\eZAccelerator\Message\Search\UnindexContent;
use Novactive\Bundle\eZAccelerator\Message\Search\UnindexLocation;

class AsyncSearchIndexer implements Handler
{
    /**
     * @var Handler
     */
    private $syncHandler;

    /**
     * @var BusDispatcher
     */
    private $busDispatcher;

    public function __construct(Handler $handler, BusDispatcher $dispatcher)
    {
        $this->syncHandler = $handler;
        $this->busDispatcher = $dispatcher;
    }

    // Sync Methods, not yet, but they could be pass through the bus

    public function findContent(Query $query, array $languageFilter = [])
    {
        return $this->syncHandler->findContent($query, $languageFilter);
    }

    public function findSingle(Criterion $filter, array $languageFilter = [])
    {
        return $this->syncHandler->findSingle($filter, $languageFilter);
    }

    public function findLocations(LocationQuery $query, array $languageFilter = [])
    {
        return $this->syncHandler->findLocations($query, $languageFilter);
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, Criterion $filter = null)
    {
        return $this->syncHandler->suggest($prefix, $fieldPaths, $limit, $filter);
    }

    // Async Methods, then obviously through the bus

    public function indexContent(Content $content)
    {
        $this->busDispatcher->dispatch(new IndexContent($content->versionInfo->contentInfo->id));
    }

    public function deleteContent($contentId, $versionId = null)
    {
        $this->busDispatcher->dispatch(new UnindexContent($contentId, $versionId));
    }

    public function indexLocation(Location $location)
    {
        $this->busDispatcher->dispatch(new IndexLocation($location->id));
    }

    public function deleteLocation($locationId, $contentId)
    {
        $this->busDispatcher->dispatch(new UnindexLocation($locationId, $contentId));
    }

    public function purgeIndex()
    {
        $this->busDispatcher->dispatch(new PurgeIndex());
    }
}
