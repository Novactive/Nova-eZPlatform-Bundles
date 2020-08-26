<?php

/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostLoad;
use eZ\Publish\API\Repository\Repository;
use Novactive\Bundle\eZMailingBundle\Entity\eZ\ContentInterface;

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
        if (null !== $entity->getLocationId()) {
            $location = $this->repository->getLocationService()->loadLocation($entity->getLocationId());
            $content = $this->repository->getContentService()->loadContentByContentInfo($location->contentInfo);
            $entity->setLocation($location);
            $entity->setContent($content);
        }
    }
}
