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

namespace Novactive\EzMenuManager\Service;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Novactive\EzMenuManager\MenuItem\MenuItemTypeRegistry;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class MenuBuilder
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var MenuItemTypeRegistry */
    protected $menuItemTypeRegistry;

    /** @var MenuCacheService */
    protected $cache;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface     $factory
     * @param MenuItemTypeRegistry $menuItemTypeRegistry
     * @param MenuCacheService     $cache
     */
    public function __construct(
        FactoryInterface $factory,
        MenuItemTypeRegistry $menuItemTypeRegistry,
        MenuCacheService $cache
    ) {
        $this->factory              = $factory;
        $this->menuItemTypeRegistry = $menuItemTypeRegistry;
        $this->cache                = $cache;
    }

    public function build(Menu $menu)
    {
        $knpMenu = $this->createItem('root');

        $rootMenuItems = $menu->getItemsByParent();
        foreach ($rootMenuItems as $childMenuItem) {
            $this->appendChild($childMenuItem, $knpMenu);
        }

        return $knpMenu;
    }

    /**
     * @param $name
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createItem($name, array $options = [])
    {
        $defaults = [
            'extras' => ['translation_domain' => 'menu'],
        ];

        return $this->factory->createItem($name, array_merge_recursive($defaults, $options));
    }

    /**
     * @param MenuItem $menuItem
     *
     * @throws \Novactive\EzMenuManager\Exception\MenuItemTypeNotFoundException
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function appendChild(MenuItem $menuItem, ItemInterface $knpMenu)
    {
        $type   = $this->menuItemTypeRegistry->getMenuItemEntityType($menuItem);
        $parent = $knpMenu->addChild($type->toMenuItemLink($menuItem));
        foreach ($menuItem->getChildrens() as $childMenuItem) {
            $this->appendChild($childMenuItem, $parent);
        }

        return $parent;
    }
}
