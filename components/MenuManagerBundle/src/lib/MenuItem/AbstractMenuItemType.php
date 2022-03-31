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
use Knp\Menu\FactoryInterface;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use ReflectionClass;

abstract class AbstractMenuItemType implements MenuItemTypeInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var FactoryInterface */
    protected $factory;

    /**
     * AbstractMenuItemType constructor.
     */
    public function __construct(EntityManagerInterface $em, FactoryInterface $factory)
    {
        $this->em = $em;
        $this->factory = $factory;
    }

    /**
     * @throws \ReflectionException
     */
    public function createEntity(): MenuItem
    {
        $class = new ReflectionClass($this->getEntityClassName());

        return $class->newInstance();
    }

    public function createMenuItemValue(string $name): MenuItemValue
    {
        return new MenuItemValue($name, $this->factory);
    }
}
