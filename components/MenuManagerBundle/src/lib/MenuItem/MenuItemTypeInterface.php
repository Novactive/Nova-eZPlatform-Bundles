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

declare(strict_types=1);

namespace Novactive\EzMenuManager\MenuItem;

use Novactive\EzMenuManagerBundle\Entity\MenuItem;

interface MenuItemTypeInterface
{
    public function getEntityClassName(): string;

    public function toHash(MenuItem $menuItem): array;

    public function fromHash($hash): ?MenuItem;

    public function createEntity(): MenuItem;

    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue;
}
