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
            'type'     => $this->getEntityClassName(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromHash($hash): ?MenuItem
    {
        if (!is_array($hash)) {
            return null;
        }

        $menuItemRepo = $this->em->getRepository(MenuItem::class);
        $menuRepo     = $this->em->getRepository(Menu::class);

        $menuItem   = $this->getEntity(isset($hash['id']) && $hash['id'] ? $hash['id'] : null);
        $updateData = [
            'name'     => $hash['name'] ?? false,
            'url'      => $hash['url'] ?? false,
            'target'   => $hash['target'] ?? false,
            'position' => $hash['position'] ?? 0,
        ];
        $menuItem->update(array_filter($updateData));

        if (isset($hash['parentId']) && $hash['parentId']) {
            $parent = $menuItemRepo->find($hash['parentId']);
            $menuItem->setParent($parent);
        }

        if (isset($hash['menuId'])) {
            $menu = $menuRepo->find($hash['menuId']);
            if (!$menu) {
                return null;
            }
            $menuItem->setMenu($menu);
        }

        return $menuItem;
    }

    /**
     * @param $id
     *
     * @throws \ReflectionException
     *
     * @return MenuItem|null|object
     */
    protected function getEntity($id)
    {
        $menuItemRepo = $this->em->getRepository(MenuItem::class);

        $menuItem = $id ? $menuItemRepo->find($id) : null;
        if (!$menuItem) {
            $menuItem = $this->createEntity();
        }

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
