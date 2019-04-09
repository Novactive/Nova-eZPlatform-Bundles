<?php
/**
 * MC-convergence-ezp.
 *
 * @package   MC-convergence-ezp
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 */

namespace Novactive\EzMenuManagerBundle\Entity\MenuItem;

use Doctrine\ORM\Mapping as ORM;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

/**
 * Class ContainerMenuItem.
 *
 * @ORM\Entity()
 *
 * @property int $contentId
 *
 * @package Novactive\EzMenuManagerBundle\Entity\MenuItem
 */
class ContainerMenuItem extends MenuItem
{
}
