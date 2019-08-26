<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzMenuManager\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class DoctrineEventListener
{
    use CachePurgerTrait;

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->lifecycleEventHandler($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->lifecycleEventHandler($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->lifecycleEventHandler($args);
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function lifecycleEventHandler(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof Menu) {
            $this->purgeMenuCache($entity);
        }

        if ($entity instanceof MenuItem) {
            $this->purgeMenuItemCache($entity);
        }
    }
}
