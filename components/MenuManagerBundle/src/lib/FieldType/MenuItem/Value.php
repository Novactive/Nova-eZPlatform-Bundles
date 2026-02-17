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
namespace Novactive\EzMenuManager\FieldType\MenuItem;

use Ibexa\Core\FieldType\Value as BaseValue;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class Value extends BaseValue implements \Stringable
{
    /**
     * Construct a new Value object and initialize it $text.
     */
    public function __construct(/**
     * Text content.
     *
     * @var MenuItem\ContentMenuItem[]
     */
    public array $menuItems = [])
    {
        parent::__construct();
    }

    /**
     * @see \Ibexa\Core\FieldType\Value
     * @see MenuItem
     */
    public function __toString(): string
    {
        $string = '';
        foreach ($this->menuItems as $menuItem) {
            $string .= ', '.$menuItem->__toString();
        }

        return $string;
    }
}
