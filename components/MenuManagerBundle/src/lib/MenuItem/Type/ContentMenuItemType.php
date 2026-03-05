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

namespace Novactive\EzMenuManager\MenuItem\Type;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Novactive\EzMenuManager\MenuItem\MenuItemValue;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Override;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

class ContentMenuItemType extends DefaultMenuItemType
{
    protected TranslationHelper $translationHelper;
    protected ContentService $contentService;
    protected LocationService $locationService;
    protected RouterInterface $router;
    protected ?TagAwareCacheInterface $cache = null;
    protected SiteAccessServiceInterface $siteAccessService;

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setTranslationHelper(TranslationHelper $translationHelper): void
    {
        $this->translationHelper = $translationHelper;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setContentService(ContentService $contentService): void
    {
        $this->contentService = $contentService;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setCache(?TagAwareCacheInterface $cache = null): void
    {
        $this->cache = $cache;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setSiteAccessService(SiteAccessServiceInterface $siteAccessService): void
    {
        $this->siteAccessService = $siteAccessService;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getEntityClassName(): string
    {
        return MenuItem\ContentMenuItem::class;
    }

    /**
     * @param MenuItem\ContentMenuItem $menuItem
     */
    #[Override]
    public function toHash(MenuItem $menuItem): array
    {
        $hash = parent::toHash($menuItem);
        try {
            $contentInfo = $this->contentService->loadContentInfo($menuItem->getContentId());
        } catch (NotFoundException|UnauthorizedException) {
            return $hash;
        }
        $hash['name'] = $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo);

        return $hash;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function fromHash($hash): ?MenuItem
    {
        $menuItem = parent::fromHash($hash);
        if (!$menuItem) {
            return null;
        }

        $menuItem->setName('');
        if (isset($hash['parentId']) && 'auto' == $hash['parentId']) {
            $menuItem->setOption('setParentOnPublish', true);
        }

        return $menuItem;
    }

    #[Override]
    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue
    {
        try {
            $menuItemLinkInfos = $this->getMenuItemLinkInfos($menuItem);
            $link = $this->createMenuItemValue($menuItemLinkInfos['id']);
            if (true === $menuItem->getOption('active', true)) {
                $link->setUri($menuItemLinkInfos['uri']);
            }
            $link->setLabel($menuItemLinkInfos['label']);
            $link->setExtras($menuItemLinkInfos['extras']);

            return $link;
        } catch (UnauthorizedException|Throwable $e) {
            return null;
        }
    }

    /**
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException|CacheException
     */
    protected function getMenuItemLinkInfos(MenuItem $menuItem): array
    {
        $siteAccess = $this->siteAccessService->getCurrent();
        $siteAccessName = '';
        if ($siteAccess instanceof \Ibexa\Core\MVC\Symfony\SiteAccess) {
            $siteAccessName = $siteAccess->name;
        }

        // Try to get from cache if cache is available
        $cacheItem = null;
        if (null !== $this->cache) {
            try {
                $cacheItem = $this->cache->getItem("content-menu-item-link-{$menuItem->getId()}-{$siteAccessName}");
                if ($cacheItem->isHit()) {
                    return $cacheItem->get();
                }
            } catch (Exception $e) {
                // Cache failed, continue without it
                $cacheItem = null;
            }
        }

        if (!$menuItem instanceof MenuItem\ContentMenuItem) {
            throw new RuntimeException(sprintf('%s only works with ContentMenuItem', __METHOD__));
        }
        $content = $this->contentService->loadContent($menuItem->getContentId());
        $location = $this->locationService->loadLocation($content->contentInfo->mainLocationId);
        $parameters = ['location' => $location];
        if ($siteAccessName) {
            $parameters['siteaccess'] = $siteAccessName;
        }

        $menuItemLinkInfos = [
            'id' => "location-{$location->id}",
            'uri' => $this->router->generate(
                UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
                $parameters,
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'label' => $this->translationHelper->getTranslatedContentNameByContentInfo($content->contentInfo),
            'extras' => [
                'contentId' => $location->contentId,
                'locationId' => $location->id,
                'locationRemoteId' => $location->remoteId,
            ],
        ];

        // Save to cache if cache is available
        if (null !== $this->cache && null !== $cacheItem) {
            try {
                $cacheItem->set($menuItemLinkInfos);
                $cacheItem->tag(
                    [
                        'content-'.$content->id,
                        'location-'.$location->id,
                        'menu-item-'.$menuItem->getId(),
                        'menu-'.$menuItem->getMenu()->getId(),
                    ]
                );
                $this->cache->save($cacheItem);
            } catch (Exception $e) {
                // Cache save failed, continue without it
            }
        }

        return $menuItemLinkInfos;
    }
}
