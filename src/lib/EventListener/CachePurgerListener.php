<?php
/**
 * @copyright Novactive
 * Date: 19/07/18
 */

namespace Novactive\EzMenuManager\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\HttpCache\Handler\TagHandler;
use Novactive\EzMenuManagerBundle\Entity\Menu;

class CachePurgerListener
{
    /** @var TagHandler */
    protected $tagHandler;

    /**
     * CachePurgerListener constructor.
     *
     * @param TagHandler $tagHandler
     */
    public function __construct(TagHandler $tagHandler)
    {
        $this->tagHandler = $tagHandler;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->purgeMenuCache($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->purgeMenuCache($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->purgeMenuCache($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function purgeMenuCache(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Menu) {
            return;
        }

        $this->tagHandler->invalidateTags(['menu-'.$entity->getId()]);
    }
}
