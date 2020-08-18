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

use Doctrine\ORM\EntityManagerInterface;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

abstract class AbstractMenuItemType implements MenuItemTypeInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * AbstractMenuItemType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws \ReflectionException
     *
     * @return MenuItem
     */
    public function createEntity(): MenuItem
    {
        $class = new \ReflectionClass($this->getEntityClassName());

        return $class->newInstance();
    }
}
