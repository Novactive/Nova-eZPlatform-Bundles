<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\HttpCache\Handler\TagHandler;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

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
        if ($entity instanceof Menu) {
            $this->tagHandler->invalidateTags(['menu-'.$entity->getId()]);
        }
        if ($entity instanceof MenuItem) {
            $this->tagHandler->invalidateTags(['menu-'.$entity->getMenu()->getId()]);
        }
    }
}
