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
    /**
     * @return string
     */
    public function getEntityClassName(): string;

    /**
     * @param MenuItem $menuItem
     *
     * @return array
     */
    public function toHash(MenuItem $menuItem): array;

    /**
     * @param $hash
     *
     * @return MenuItem
     */
    public function fromHash($hash): ?MenuItem;

    /**
     * @return MenuItem
     */
    public function createEntity(): MenuItem;

    /**
     * @param MenuItem $menuItem
     *
     * @return MenuItemValue
     */
    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue;
}
