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

use Novactive\EzMenuManagerBundle\Entity\MenuItem;

interface MenuItemTypeInterface
{
    public function getEntityClassName(): string;

    public function toHash(MenuItem $menuItem): array;

    /**
     * @param $hash
     *
     * @return MenuItem
     */
    public function fromHash($hash): ?MenuItem;

    public function createEntity(): MenuItem;

    /**
     * @return MenuItemValue
     */
    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue;
}
