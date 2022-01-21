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

use InvalidArgumentException;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem as KnpMenuItem;

class MenuItemValue extends KnpMenuItem
{
    public const TARGET_BLANK = '_blank';
    public const TARGET_SELF = '_self';
    public const TARGET_PARENT = '_parent';
    public const TARGET_TOP = '_top';

    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, FactoryInterface $factory)
    {
        parent::__construct($name, $factory);
    }

    public function addChild($child, array $options = []): ItemInterface
    {
        if (!$child instanceof ItemInterface) {
            throw new InvalidArgumentException(
                'Cannot add menu item as child, if it doesn\'t implement ItemInterface.'
            );
        }
        if (null !== $child->getParent()) {
            throw new InvalidArgumentException(
                'Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).'
            );
        }

        $child->setParent($this);

        $this->children[$child->getName()] = $child;

        return $child;
    }

    /**
     * @param $target
     */
    public function setTarget($target)
    {
        $this->setLinkAttribute('target', $target);
    }

    /**
     * @return mixed|null
     */
    public function getTarget()
    {
        return $this->getLinkAttribute('target');
    }

    public function setTitle($title)
    {
        $this->setLinkAttribute('title', $title);
    }

    /**
     * @return mixed|null
     */
    public function getTitle()
    {
        return $this->getLinkAttribute('title');
    }
}
