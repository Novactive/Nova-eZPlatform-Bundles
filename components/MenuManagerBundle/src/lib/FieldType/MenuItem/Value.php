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

namespace Novactive\EzMenuManager\FieldType\MenuItem;

use eZ\Publish\Core\FieldType\Value as BaseValue;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class Value extends BaseValue
{
    /**
     * Text content.
     *
     * @var MenuItem\ContentMenuItem[]
     */
    public $menuItems;

    /**
     * Construct a new Value object and initialize it $text.
     *
     * @param array $menuItems
     */
    public function __construct($menuItems = [])
    {
        $this->menuItems = $menuItems;
    }

    /**
     * @see \eZ\Publish\Core\FieldType\Value
     */
    public function __toString()
    {
        return (string) $this->menuItems;
    }
}
