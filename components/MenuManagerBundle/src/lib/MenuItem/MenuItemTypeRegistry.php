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

use Novactive\EzMenuManager\Exception\MenuItemTypeNotFoundException;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class MenuItemTypeRegistry
{
    /** @var MenuItemTypeInterface[] */
    protected $menuItemTypes = [];

    /**
     * MenuItemTypeRegistry constructor.
     *
     * @param MenuItemTypeInterface|iterable $menuItemTypes
     */
    public function __construct(iterable $menuItemTypes)
    {
        foreach ($menuItemTypes as $menuItemType) {
            $this->addMenuItemType($menuItemType);
        }
    }

    /**
     * @param MenuItemTypeInterface $menuItemType
     */
    public function addMenuItemType(MenuItemTypeInterface $menuItemType): void
    {
        $this->menuItemTypes[$menuItemType->getEntityClassName()] = $menuItemType;
    }

    /**
     * @return array
     */
    public function getMenuItemTypesIdentifier(): array
    {
        return array_keys($this->menuItemTypes);
    }

    /**
     * @return MenuItemTypeInterface[]
     */
    public function getMenuItemTypes(): array
    {
        return $this->menuItemTypes;
    }

    /**
     * @param MenuItem $entity
     *
     * @throws MenuItemTypeNotFoundException
     *
     * @return MenuItemTypeInterface
     */
    public function getMenuItemEntityType(MenuItem $entity)
    {
        $className = get_class($entity);

        return $this->getMenuItemType($className);
    }

    /**
     * @param $entityClassName
     *
     * @throws MenuItemTypeNotFoundException
     *
     * @return MenuItemTypeInterface
     */
    public function getMenuItemType($entityClassName)
    {
        if (!isset($this->menuItemTypes[$entityClassName])) {
            throw new MenuItemTypeNotFoundException($entityClassName);
        }

        return $this->menuItemTypes[$entityClassName];
    }
}
