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

namespace Novactive\EzMenuManager\Service\DataTransformer;

use Novactive\EzMenuManager\MenuItem\MenuItemConverter;
use Symfony\Component\Form\DataTransformerInterface;

class MenuItemsCollectionTransformer implements DataTransformerInterface
{
    /** @var MenuItemConverter */
    protected $menuItemConverter;

    /**
     * MenuItemsCollection constructor.
     */
    public function __construct(MenuItemConverter $menuItemConverter)
    {
        $this->menuItemConverter = $menuItemConverter;
    }

    /**
     * Transforms a FieldType Value into a hash using `FieldTpe::toHash()`.
     * This hash is compatible with `reverseTransform()`.
     *
     * @return array|null the value's hash, or null if $value was not a FieldType Value
     */
    public function transform($value)
    {
        return json_encode($this->menuItemConverter->toHashArray($value->getValues()));
    }

    /**
     * Transforms a hash into a FieldType Value using `FieldType::fromHash()`.
     * The FieldValue is compatible with `transform()`.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function reverseTransform($value)
    {
        return $this->menuItemConverter->fromHashArray(json_decode($value, true));
    }
}
