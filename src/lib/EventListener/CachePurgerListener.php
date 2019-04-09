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
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class CachePurgerListener
{
    /** @var TagHandler */
    protected $httpCache;

    /** @var TagAwareAdapterInterface */
    protected $persistenceCache;

    /**
     * CachePurgerListener constructor.
     *
     * @param TagHandler               $httpCache
     * @param TagAwareAdapterInterface $persistenceCache
     */
    public function __construct(TagHandler $httpCache, TagAwareAdapterInterface $persistenceCache)
    {
        $this->httpCache        = $httpCache;
        $this->persistenceCache = $persistenceCache;
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
        $tags   = [];

        if ($entity instanceof Menu) {
            $tags = ['menu-'.$entity->getId()];
        }

        if ($entity instanceof MenuItem) {
            $tags = [
                'menu-item-'.$entity->getId(),
                'menu-'.$entity->getMenu()->getId(),
            ];
        }

        if (!empty($tags)) {
            $this->httpCache->invalidateTags($tags);
            $this->persistenceCache->invalidateTags($tags);
        }
    }
}
