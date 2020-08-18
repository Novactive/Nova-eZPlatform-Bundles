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

namespace Novactive\EzMenuManager\FieldType\MenuItem;

use Novactive\EzMenuManager\MenuItem\MenuItemTypeRegistry;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class ValueConverter
{
    /** @var MenuItemTypeRegistry */
    protected $menuItemTypeRegistry;

    /**
     * ValueConverter constructor.
     */
    public function __construct(MenuItemTypeRegistry $menuItemTypeRegistry)
    {
        $this->menuItemTypeRegistry = $menuItemTypeRegistry;
    }

    public function toHash(Value $value): array
    {
        $hash = [];
        foreach ($value->menuItems as $menuItem) {
            $type = $this->menuItemTypeRegistry->getMenuItemType(MenuItem\ContentMenuItem::class);
            $hash[] = $type->toHash($menuItem);
        }

        return $hash;
    }

    /**
     * @param $hash
     */
    public function fromHash($hash): Value
    {
        if (!is_array($hash)) {
            return new Value();
        }

        $menuItems = [];
        foreach ($hash as $hashItem) {
            $type = $this->menuItemTypeRegistry->getMenuItemType(MenuItem\ContentMenuItem::class);
            $menuItem = $type->fromHash($hashItem);
            if ($menuItem) {
                $menuItems[] = $menuItem;
            }
        }

        return new Value($menuItems);
    }
}
