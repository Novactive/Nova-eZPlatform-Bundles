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

namespace Novactive\EzMenuManager\Twig;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Helper\TranslationHelper;
use Knp\Menu\Twig\Helper;
use Novactive\EzMenuManager\MenuItem\MenuItemConverter;
use Novactive\EzMenuManager\Service\MenuBuilder;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

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

    /** @var MenuItemConverter */
    protected $menuItemConverter;

    /**
     * MenuManagerExtension constructor.
     *
     * @param TranslationHelper $translationHelper
     * @param ContentService    $contentService
     * @param MenuBuilder       $menuBuilder
     * @param Helper            $knpHelper
     * @param MenuItemConverter $menuItemConverter
     */
    public function __construct(
        TranslationHelper $translationHelper,
        ContentService $contentService,
        MenuBuilder $menuBuilder,
        Helper $knpHelper,
        MenuItemConverter $menuItemConverter
    ) {
        $this->translationHelper = $translationHelper;
        $this->contentService    = $contentService;
        $this->menuBuilder       = $menuBuilder;
        $this->knpHelper         = $knpHelper;
        $this->menuItemConverter = $menuItemConverter;
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
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        $functions   = parent::getFunctions();
        $functions[] = new \Twig_SimpleFilter('sort_menu_items_by_menu', [$this, 'sortMenuItemsByMenu']);

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
        $this->addMenuItemToBreadcrumb($menuItem, $breadcrumb);

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
                'id'     => 'root',
                'parent' => '#',
                'text'   => $menu->getName(),
                'state'  => [
                    'disabled' => false,
                    'opened'   => true,
                ],
                'type' => 'root',
            ],
        ];

        foreach ($menu->getItems() as $menuItem) {
            $hash   = $this->menuItemConverter->toHash($menuItem);
            $list[] = [
                'id'     => $hash['id'],
                'parent' => $hash['parentId'] ? $hash['parentId'] : 'root',
                'text'   => $hash['name'],
                'type'   => $hash['type'],
                'state'  => [
                    'disabled' => false,
                    'opened'   => true,
                ],
            ];
        }

        return $list;
    }

    /**
     * @param MenuItem[] $menuItems
     *
     * @return array
     */
    public function sortMenuItemsByMenu(array $menuItems)
    {
        $menus = [];
        foreach ($menuItems as $menuItem) {
            $menu = $menuItem->getMenu();
            if (!isset($menus[$menu->getId()])) {
                $menus[$menu->getId()] = [
                    'menu'  => $menu,
                    'items' => [],
                ];
            }
            $menus[$menu->getId()]['items'][] = $menuItem;
        }

        return $menus;
    }
}
