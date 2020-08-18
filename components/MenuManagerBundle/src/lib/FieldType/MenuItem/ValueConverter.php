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
     *
     * @param MenuItemTypeRegistry $menuItemTypeRegistry
     */
    public function __construct(MenuItemTypeRegistry $menuItemTypeRegistry)
    {
        $this->menuItemTypeRegistry = $menuItemTypeRegistry;
    }

    /**
     * @param Value $value
     *
     * @return array
     */
    public function toHash(Value $value): array
    {
        $hash = [];
        foreach ($value->menuItems as $menuItem) {
            $type   = $this->menuItemTypeRegistry->getMenuItemType(MenuItem\ContentMenuItem::class);
            $hash[] = $type->toHash($menuItem);
        }

        return $hash;
    }

    /**
     * @param $hash
     *
     * @return Value
     */
    public function fromHash($hash): Value
    {
        if (!is_array($hash)) {
            return new Value();
        }

        $menuItems = [];
        foreach ($hash as $hashItem) {
            $type     = $this->menuItemTypeRegistry->getMenuItemType(MenuItem\ContentMenuItem::class);
            $menuItem = $type->fromHash($hashItem);
            if ($menuItem) {
                $menuItems[] = $menuItem;
            }
        }

        return new Value($menuItems);
    }
}
