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

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem as KnpMenuItem;
use Novactive\EzMenuManager\MenuItem\AbstractMenuItemType;
use Novactive\EzMenuManager\MenuItem\MenuItemTypeInterface;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class DefaultMenuItemType extends AbstractMenuItemType implements MenuItemTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClassName(): string
    {
        return MenuItem::class;
    }

    /**
     * {@inheritdoc}
     */
    public function toHash(MenuItem $menuItem): array
    {
        $parent = $menuItem->getParent();

        return [
            'id'       => $menuItem->getId(),
            'menuId'   => $menuItem->getMenu()->getId(),
            'parentId' => $parent ? $parent->getId() : null,
            'position' => $menuItem->getPosition(),
            'url'      => $menuItem->getUrl(),
            'name'     => $menuItem->getName(),
            'target'   => $menuItem->getTarget(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromHash($hash): MenuItem
    {
        if (!is_array($hash)) {
            return null;
        }

        $menuRepo     = $this->em->getRepository(Menu::class);
        $menuItemRepo = $this->em->getRepository(MenuItem::class);
        if (isset($hash['id']) && $hash['id']) {
            $menuItem = $menuItemRepo->find($hash['id']);
        } else {
            $menuItem = $this->createEntity();
        }
        $menuItem->setPosition($hash['position'] ?? 0);

        if (isset($hash['parentId']) && $hash['parentId'] && $parent = $menuItemRepo->find($hash['parentId'])) {
            $menuItem->setParent($parent);
        }

        $menu = $menuRepo->find($hash['menuId']);
        $menuItem->setMenu($menu);

        return $menuItem;
    }

    /**
     * @inheritDoc
     */
    public function toMenuItemLink(MenuItem $menuItem): ?ItemInterface
    {
        $link = new KnpMenuItem($menuItem->getName(), $this->factory);
        $link->setUri($menuItem->getUrl());
        $link->setLinkAttribute('target', $menuItem->getTarget());

        return $link;
    }
}
