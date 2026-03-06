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

declare(strict_types=1);

namespace Novactive\EzMenuManager\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface;
use Ibexa\Core\Persistence\Cache\Adapter\TransactionAwareAdapterInterface;
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

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setHttpCachePurgeClient(PurgeClientInterface $httpCachePurgeClient): void
    {
        $this->httpCachePurgeClient = $httpCachePurgeClient;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setPersistenceCache(TransactionAwareAdapterInterface $persistenceCacheAdapter): void
    {
        $this->persistenceCacheAdapter = $persistenceCacheAdapter;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    protected function purgeMenuCache(Menu $entity): void
    {
        $tags = ['menu-'.$entity->getId()];
        $this->invalidateTags($tags);
    }

    protected function purgeMenuItemCache(MenuItem $entity): void
    {
        $tags = [
            'menu-item-'.$entity->getId(),
            'menu-'.$entity->getMenu()->getId(),
        ];
        $this->invalidateTags($tags);
    }

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

    protected function invalidateTags(array $tags): void
    {
        if (empty($tags)) {
            return;
        }

        // Try HTTP cache purge if available
        if ($this->httpCachePurgeClient && method_exists($this->httpCachePurgeClient, 'invalidateTags')) {
            $this->httpCachePurgeClient->invalidateTags($tags);
        }

        // Try persistence cache if available
        if ($this->persistenceCacheAdapter && method_exists($this->persistenceCacheAdapter, 'invalidateTags')) {
            $this->persistenceCacheAdapter->invalidateTags($tags);
        }
    }
}
