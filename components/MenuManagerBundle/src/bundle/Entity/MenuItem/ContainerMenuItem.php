<?php

declare(strict_types=1);

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

#[ORM\Entity]
class ContainerMenuItem extends MenuItem
{
}