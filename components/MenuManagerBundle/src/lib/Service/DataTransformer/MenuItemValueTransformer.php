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

use Novactive\EzMenuManager\FieldType\MenuItem\ValueConverter;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class MenuItemValueTransformer.
 *
 * @package Novactive\EzMenuManager\Service
 */
class MenuItemValueTransformer implements DataTransformerInterface
{
    /** @var ValueConverter */
    protected $valueConverter;

    /**
     * FieldValueTransformer constructor.
     */
    public function __construct(ValueConverter $valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * Transforms a FieldType Value into a hash using `FieldTpe::toHash()`.
     * This hash is compatible with `reverseTransform()`.
     *
     * @return array|null the value's hash, or null if $value was not a FieldType Value
     */
    public function transform($value)
    {
        return json_encode($this->valueConverter->toHash($value));
    }

    /**
     * Transforms a hash into a FieldType Value using `FieldType::fromHash()`.
     * The FieldValue is compatible with `transform()`.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function reverseTransform($value)
    {
        $hash = json_decode($value, true);

        return $this->valueConverter->fromHash($hash);
    }
}
