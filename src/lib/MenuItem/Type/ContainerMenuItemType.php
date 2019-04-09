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

namespace Novactive\EzMenuManager\MenuItem\Type;

use Novactive\EzMenuManager\MenuItem\MenuItemValue;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class ContainerMenuItemType extends DefaultMenuItemType
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClassName(): string
    {
        return MenuItem\ContainerMenuItem::class;
    }

    /**
     * @inheritDoc
     */
    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue
    {
        $name = $this->getName($menuItem);
        if (null === $name) {
            return null;
        }

        return new MenuItemValue($name);
    }
}
