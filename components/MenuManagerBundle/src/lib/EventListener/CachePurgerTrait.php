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
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

trait CachePurgerTrait
{
    /** @var PurgeClientInterface */
    protected $httpCachePurgeClient;

    /** @var TransactionAwareAdapterInterface */
    protected $persistenceCacheAdapter;

    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @required
     */
    public function setHttpCachePurgeClient(PurgeClientInterface $httpCachePurgeClient): void
    {
        $this->httpCachePurgeClient = $httpCachePurgeClient;
    }

    /**
     * @required
     */
    public function setPersistenceCache(TransactionAwareAdapterInterface $persistenceCacheAdapter): void
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
            $this->httpCachePurgeClient->purge($tags);
            $this->persistenceCacheAdapter->invalidateTags($tags);
        }
    }
}
