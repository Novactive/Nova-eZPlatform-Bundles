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

namespace Novactive\EzMenuManager\MenuItem\Type;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Helper\TranslationHelper;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem as KnpMenuItem;
use Novactive\EzMenuManager\MenuItem\MenuItemTypeInterface;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Routing\RouterInterface;

class ContentMenuItemType extends DefaultMenuItemType implements MenuItemTypeInterface
{
    /** @var TranslationHelper */
    protected $translationHelper;

    /** @var ContentService */
    protected $contentService;

    /** @var LocationService */
    protected $locationService;

    /** @var RouterInterface */
    protected $router;

    /**
     * ContentMenuItemType constructor.
     *
     * @param EntityManagerInterface   $em
     * @param FactoryInterface         $factory
     * @param TranslationHelper        $translationHelper
     * @param ContentService           $contentService
     * @param LocationService          $locationService
     * @param RouterInterface          $router
     * @param TagAwareAdapterInterface $cache
     */
    public function __construct(
        EntityManagerInterface $em,
        FactoryInterface $factory,
        TranslationHelper $translationHelper,
        ContentService $contentService,
        LocationService $locationService,
        RouterInterface $router,
        TagAwareAdapterInterface $cache
    ) {
        parent::__construct($em, $factory);
        $this->translationHelper = $translationHelper;
        $this->contentService    = $contentService;
        $this->locationService   = $locationService;
        $this->router            = $router;
        $this->cache             = $cache;
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
     *
     * @return array
     */
    public function toHash(MenuItem $menuItem): array
    {
        $hash = parent::toHash($menuItem);
        try {
            $contentInfo = $this->contentService->loadContentInfo($menuItem->getContentId());
        } catch (NotFoundException $exception) {
            return $hash;
        }
        $hash['name'] = $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo);

        return $hash;
    }

    /**
     * @inheritDoc
     */
    public function fromHash($hash): MenuItem
    {
        $menuItem = parent::fromHash($hash);
        if (isset($hash['parentId']) && 'auto' == $hash['parentId']) {
            $menuItem->setOption('setParentOnPublish', true);
        }

        return $menuItem;
    }

    /**
     * @param MenuItem $menuItem
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @return ItemInterface
     */
    public function toMenuItemLink(MenuItem $menuItem): ?ItemInterface
    {
        try {
            $contentInfo = $this->contentService->loadContentInfo($menuItem->getContentId());
        } catch (NotFoundException $e) {
            return null;
        }

        $location = $this->locationService->loadLocation($contentInfo->mainLocationId);

        $cacheItem = $this->cache->getItem('content-menu-item-link-'.$menuItem->getId());
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $link = new KnpMenuItem('location-'.$location->id, $this->factory);
        $link->setUri($this->router->generate($location));
        $link->setLabel($this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo));
        $link->setExtras(
            [
                'contentId' => $location->contentId,
                'locationId'=> $location->id,
                'title'     => $this->contentService->loadContent($location->contentId)->getFieldValue('title')
            ]
        );
        $cacheItem->set($link);
        $cacheItem->tag(
            [
                'content-'.$contentInfo->id,
                'location-'.$location->id,
                'menu-item-'.$menuItem->getId(),
                'menu-'.$menuItem->getMenu()->getId(),
            ]
        );
        $this->cache->save($cacheItem);

        return $link;
    }
}
