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

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\Symfony\siteAccess;
use eZ\Publish\Core\Repository\siteAccessAware\ContentService;
use eZ\Publish\Core\Repository\siteAccessAware\LocationService;
use Novactive\EzMenuManager\MenuItem\MenuItemValue;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ContentMenuItemType extends DefaultMenuItemType
{
    /** @var TranslationHelper */
    protected $translationHelper;

    /** @var ContentService */
    protected $contentService;

    /** @var LocationService */
    protected $locationService;

    /** @var RouterInterface */
    protected $router;

    /** @var TagAwareAdapterInterface */
    protected $cache;

    /** @var SiteAccess */
    protected $siteAccess;

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
        } catch (NotFoundException | UnauthorizedException $exception) {
            return $hash;
        }
        $hash['name'] = $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo);

        return $hash;
    }

    /**
     * @inheritDoc
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
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @return MenuItemValue
     */
    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue
    {
        try {
            $menuItemLinkInfos = $this->getMenuItemLinkInfos($menuItem);
            $link = new MenuItemValue($menuItemLinkInfos['id']);
            if (true === $menuItem->getOption('active', true)) {
                $link->setUri($menuItemLinkInfos['uri']);
            }
            $link->setLabel($menuItemLinkInfos['label']);
            $link->setExtras($menuItemLinkInfos['extras']);

            return $link;
        } catch (UnauthorizedException $e) {
            return null;
        } catch (NotFoundException $e) {
            return null;
        }

        return null;
    }

    /**
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function getMenuItemLinkInfos(MenuItem $menuItem): array
    {
        $cacheItem = $this->cache->getItem("content-menu-item-link-{$menuItem->getId()}-{$this->siteAccess->name}");
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $content = $this->contentService->loadContent($menuItem->getContentId());
        $location = $this->locationService->loadLocation($content->contentInfo->mainLocationId);

        $menuItemLinkInfos = [
            'id' => "location-{$location->id}",
            'uri' => $this->router->generate($location, [], UrlGeneratorInterface::ABSOLUTE_URL),
            'label' => $this->translationHelper->getTranslatedContentNameByContentInfo($content->contentInfo),
            'extras' => [
                    'contentId' => $location->contentId,
                    'locationId' => $location->id,
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
