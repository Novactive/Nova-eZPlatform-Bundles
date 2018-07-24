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

namespace Novactive\EzMenuManager\Twig;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Helper\TranslationHelper;
use Knp\Menu\Twig\Helper;
use Novactive\EzMenuManager\Service\MenuBuilder;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;

class MenuManagerExtension extends \Twig_Extension
{
    /** @var TranslationHelper */
    protected $translationHelper;

    /** @var ContentService */
    protected $contentService;

    /** @var MenuBuilder */
    protected $menuBuilder;

    /** @var Helper */
    protected $knpHelper;

    /**
     * MenuManagerExtension constructor.
     *
     * @param TranslationHelper $translationHelper
     * @param ContentService    $contentService
     * @param MenuBuilder       $menuBuilder
     * @param Helper            $knpHelper
     */
    public function __construct(
        TranslationHelper $translationHelper,
        ContentService $contentService,
        MenuBuilder $menuBuilder,
        Helper $knpHelper
    ) {
        $this->translationHelper = $translationHelper;
        $this->contentService    = $contentService;
        $this->menuBuilder       = $menuBuilder;
        $this->knpHelper         = $knpHelper;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        $functions   = parent::getFunctions();
        $functions[] = new \Twig_SimpleFunction('ezmenumanager_menu_jstree', [$this, 'getMenuJstree']);
        $functions[] = new \Twig_SimpleFunction('ezmenumanager_breadcrumb', [$this, 'buildBreadcrumb']);

        return $functions;
    }

    /**
     * @param MenuItem $menuItem
     *
     * @return mixed
     */
    public function addMenuItemToBreadcrumb(MenuItem $menuItem, &$breadcrumb = [])
    {
        $breadcrumb[] = $this->menuBuilder->toMenuItemLink($menuItem);
        if ($parent = $menuItem->getParent()) {
            $this->addMenuItemToBreadcrumb($parent, $breadcrumb);
        }

        return $breadcrumb;
    }

    public function buildBreadcrumb(MenuItem $menuItem)
    {
        $breadcrumb = [];
        if ($parent = $menuItem->getParent()) {
            $this->addMenuItemToBreadcrumb($parent, $breadcrumb);
        }

        return array_reverse($breadcrumb);
    }

    /**
     * @param Menu $menu
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return array
     */
    public function getMenuJstree(Menu $menu)
    {
        $list = [
            [
                'id'     => 0,
                'parent' => '#',
                'text'   => $menu->getName(),
                'state'  => [
                    'disabled' => false,
                    'opened'   => true,
                ],
            ],
        ];

        foreach ($menu->getItems() as $menuItem) {
            $parent = $menuItem->getParent();
            $name   = $menuItem->getName();
            if ($menuItem instanceof ContentMenuItem) {
                try {
                    $content = $this->contentService->loadContent($menuItem->getContentId());
                    $name    = $this->translationHelper->getTranslatedContentName($content);
                } catch (NotFoundException $exception) {
                    $name = $menuItem->getUrl();
                }
            }
            $list[] = [
                'id'     => $menuItem->getId(),
                'parent' => $parent ? $parent->getId() : 0,
                'text'   => $name,
                'state'  => [
                    'disabled' => false,
                    'opened'   => true,
                ],
            ];
        }

        return $list;
    }
}
