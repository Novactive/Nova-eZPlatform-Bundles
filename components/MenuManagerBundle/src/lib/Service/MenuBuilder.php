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

namespace Novactive\EzMenuManager\Service;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Novactive\EzMenuManager\MenuItem\MenuItemTypeRegistry;
use Novactive\EzMenuManager\MenuItem\MenuItemValue;
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
     */
    public function __construct(
        FactoryInterface $factory,
        MenuItemTypeRegistry $menuItemTypeRegistry,
        MenuCacheService $cache
    ) {
        $this->factory = $factory;
        $this->menuItemTypeRegistry = $menuItemTypeRegistry;
        $this->cache = $cache;
    }

    public function build(Menu $menu, $parent = null)
    {
        $knpMenu = $this->createItem('root');

        $rootMenuItems = $menu->getItemsByParent($parent);
        foreach ($rootMenuItems as $childMenuItem) {
            $this->appendChild($childMenuItem, $knpMenu);
        }

        return $knpMenu;
    }

    /**
     * @param $name
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
     * @throws \Novactive\EzMenuManager\Exception\MenuItemTypeNotFoundException
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function appendChild(MenuItem $menuItem, ItemInterface $knpMenu)
    {
        $menuItemLink = $this->toMenuItemLink($menuItem);
        if (null !== $menuItemLink) {
            $parent = $knpMenu->addChild($menuItemLink);
            foreach ($menuItem->getChildrens() as $childMenuItem) {
                $this->appendChild($childMenuItem, $parent);
            }
        }
    }

    /**
     * @throws \Novactive\EzMenuManager\Exception\MenuItemTypeNotFoundException
     */
    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue
    {
        $type = $this->menuItemTypeRegistry->getMenuItemEntityType($menuItem);

        return $type->toMenuItemLink($menuItem);
    }
}
