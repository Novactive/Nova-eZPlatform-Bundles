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

namespace Novactive\EzMenuManager\MenuItem;

use Novactive\EzMenuManager\Exception\UnexpectedTypeException;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class MenuItemConverter
{
    /** @var MenuItemTypeRegistry */
    protected $menuItemTypeRegistry;

    /**
     * MenuItemConverter constructor.
     */
    public function __construct(MenuItemTypeRegistry $menuItemTypeRegistry)
    {
        $this->menuItemTypeRegistry = $menuItemTypeRegistry;
    }

    public function toHash(MenuItem $menuItem): array
    {
        $type = $this->menuItemTypeRegistry->getMenuItemEntityType($menuItem);

        return $type->toHash($menuItem);
    }

    /**
     * @param $hash
     *
     * @return MenuItem
     */
    public function fromHash($hash, $defaultClass = MenuItem::class): ?MenuItem
    {
        $type = $this->menuItemTypeRegistry->getMenuItemType($defaultClass);

        return $type->fromHash($hash);
    }

    /**
     * @param $menuItems
     *
     * @throws UnexpectedTypeException
     */
    public function toHashArray($menuItems): array
    {
        if (!is_array($menuItems) && !($menuItems instanceof \Traversable && $menuItems instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($menuItems, 'array or (\Traversable and \ArrayAccess)');
        }
        $hash = [];
        foreach ($menuItems as $menuItem) {
            $hash[] = $this->toHash($menuItem);
        }

        return $hash;
    }

    /**
     * @param string $defaultClass
     *
     * @SuppressWarnings(PHPMD.IfStatementAssignment)
     *
     * @return MenuItem[]
     */
    public function fromHashArray(array $hashArray, $defaultClass = MenuItem::class): array
    {
        /** @var MenuItem[] $menuItems */
        $menuItems = [];
        foreach ($hashArray as $hashItem) {
            $menuItem = $this->fromHash($hashItem, isset($hashItem['type']) ? $hashItem['type'] : $defaultClass);
            if ($menuItem) {
                $menuItems[$hashItem['id']] = $menuItem;
                if (
                    !$menuItem->getParent()
                    && 0 === strpos($hashItem['parentId'], '_')
                    && ($parent = $menuItems[$hashItem['parentId']] ?? null)
                ) {
                    $parent->addChildren($menuItem);
                }
            }
        }

        return $menuItems;
    }
}
