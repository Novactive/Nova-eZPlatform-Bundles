<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use FOS\HttpCache\Handler\TagHandler;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

trait CachePurgerTrait
{
    /** @var TagHandler */
    protected $httpCacheTagHandler;

    /** @var TagAwareAdapterInterface */
    protected $persistenceCacheAdapter;

    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @required
     */
    public function setHttpCache(TagHandler $httpCacheTagHandler): void
    {
        $this->httpCacheTagHandler = $httpCacheTagHandler;
    }

    /**
     * @required
     */
    public function setPersistenceCache(TagAwareAdapterInterface $persistenceCacheAdapter): void
    {
        $this->persistenceCacheAdapter = $persistenceCacheAdapter;
    }

    /**
     * @required
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function purgeMenuCache(Menu $entity): void
    {
        $tags = ['menu-'.$entity->getId()];
        $this->invalidateTags($tags);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function purgeMenuItemCache(MenuItem $entity): void
    {
        $tags = [
            'menu-item-'.$entity->getId(),
            'menu-'.$entity->getMenu()->getId(),
        ];
        $this->invalidateTags($tags);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function purgeContentMenuItemCache(int $contentId): void
    {
        $menuItems = $this->em->getRepository(MenuItem::class)->findBy(
            [
                'url' => MenuItem\ContentMenuItem::URL_PREFIX.$contentId,
            ]
        );
        foreach ($menuItems as $menuItem) {
            $this->purgeMenuItemCache($menuItem);
        }
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function invalidateTags(array $tags): void
    {
        if (!empty($tags)) {
            $this->httpCacheTagHandler->invalidateTags($tags);
            $this->persistenceCacheAdapter->invalidateTags($tags);
        }
    }
}
