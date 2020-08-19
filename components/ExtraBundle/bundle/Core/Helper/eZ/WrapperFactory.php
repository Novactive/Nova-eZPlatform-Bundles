<?php

/**
 * NovaeZExtraBundle Wrapper Factory.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\API\Repository\Values\Content\Location as ValueLocation;

final class WrapperFactory
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $eZRepo)
    {
        $this->repository = $eZRepo;
    }

    /**
     * @param ValueContent|int  $contentId
     * @param ValueLocation|int $locationId
     */
    public function create($contentId, $locationId, array $extra = []): Wrapper
    {
        $wrapper = new Wrapper($contentId, $locationId, $extra);

        return $wrapper->setRepository($this->repository);
    }

    public function createByLocation(ValueLocation $location): Wrapper
    {
        $wrapper = new Wrapper($location->contentInfo->id, $location);

        return $wrapper->setRepository($this->repository);
    }

    public function createByLocationId(int $locationId): Wrapper
    {
        $wrapper = new Wrapper(null, $locationId);

        return $wrapper->setRepository($this->repository);
    }

    public function createByContent(ValueContent $content): Wrapper
    {
        $wrapper = new Wrapper($content, $content->contentInfo->mainLocationId);

        return $wrapper->setRepository($this->repository);
    }

    public function createByContentId(int $contentId): Wrapper
    {
        $wrapper = new Wrapper($contentId);

        return $wrapper->setRepository($this->repository);
    }
}
