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

namespace Novactive\EzMenuManagerBundle\Entity\MenuItem;

use Doctrine\ORM\Mapping as ORM;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

/**
 * Class LocationMenuItem.
 *
 * @ORM\Entity()
 *
 * @property int locationId
 *
 * @package Novactive\EzMenuManagerBundle\Entity\MenuItem
 */
class LocationMenuItem extends MenuItem
{
    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->getOption('locationId');
    }

    /**
     * @param int $locationId
     */
    public function setLocationId(int $locationId): void
    {
        $this->setOption('locationId', $locationId);
    }
}
