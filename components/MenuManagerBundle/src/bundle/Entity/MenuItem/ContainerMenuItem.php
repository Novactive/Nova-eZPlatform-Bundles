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
