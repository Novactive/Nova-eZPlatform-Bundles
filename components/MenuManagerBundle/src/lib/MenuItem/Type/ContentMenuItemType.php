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

namespace Novactive\EzMenuManager\MenuItem\Type;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Novactive\EzMenuManager\MenuItem\MenuItemValue;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

class ContentMenuItemType extends DefaultMenuItemType
{
    protected TranslationHelper $translationHelper;
    protected ContentService $contentService;
    protected LocationService $locationService;
    protected RouterInterface $router;
    protected TagAwareAdapterInterface $cache;
    protected SiteAccess $siteAccess;

    /**
     * @required
     */
    public function setTranslationHelper(TranslationHelper $translationHelper): void
    {
        $this->translationHelper = $translationHelper;
    }

    /**
     * @required
     */
    public function setContentService(ContentService $contentService): void
    {
        $this->contentService = $contentService;
    }

    /**
     * @required
     */
    public function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    /**
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @required
     */
    public function setCache(TagAwareAdapterInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @required
     */
    public function setsiteAccess(siteAccess $siteAccess): void
    {
        $this->siteAccess = $siteAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClassName(): string
    {
        return MenuItem\ContentMenuItem::class;
    }

    /**
     * @param MenuItem\ContentMenuItem $menuItem
     */
    public function toHash(MenuItem $menuItem): array
    {
        $hash = parent::toHash($menuItem);
        try {
            $contentInfo = $this->contentService->loadContentInfo($menuItem->getContentId());
        } catch (NotFoundException|UnauthorizedException $exception) {
            return $hash;
        }
        $hash['name'] = $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo);

        return $hash;
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * @param MenuItem $menuItem
     * @return MenuItemValue|null
     */
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
        $cacheItem = $this->cache->getItem("content-menu-item-link-{$menuItem->getId()}-{$this->siteAccess->name}");
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        if (!$menuItem instanceof MenuItem\ContentMenuItem) {
            throw new RuntimeException(sprintf("%s only works with ContentMenuItem", __METHOD__));
        }
        $content = $this->contentService->loadContent($menuItem->getContentId());
        $location = $this->locationService->loadLocation($content->contentInfo->mainLocationId);

        $menuItemLinkInfos = [
            'id' => "location-{$location->id}",
            'uri' => $this->router->generate(
                UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
                [
                    'location' => $location,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'label' => $this->translationHelper->getTranslatedContentNameByContentInfo($content->contentInfo),
            'extras' => [
                'contentId' => $location->contentId,
                'locationId' => $location->id,
                'remoteId' => $location->remoteId
            ],
        ];

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

        return $menuItemLinkInfos;
    }
}
