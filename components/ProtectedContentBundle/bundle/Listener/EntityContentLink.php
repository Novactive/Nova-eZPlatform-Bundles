<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostLoad;
use eZ\Publish\API\Repository\Repository;
use Novactive\Bundle\eZProtectedContentBundle\Entity\eZ\ContentInterface;

/**
 * Class ContentLink
 * Link an eZ Content to an Entity.
 */
class EntityContentLink
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /** @PostLoad */
    public function postLoadHandler(ContentInterface $entity, LifecycleEventArgs $event): void
    {
        $content = $this->repository->getContentService()->loadContent($entity->getContentId());
        $location = $this->repository->getLocationService()->loadLocation($content->contentInfo->mainLocationId);
        $entity->setLocation($location);
        $entity->setContent($content);
    }
}
