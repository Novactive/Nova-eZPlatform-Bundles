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
namespace Novactive\EzMenuManager\Service\DataTransformer;

use Novactive\EzMenuManager\Exception\MenuItemTypeNotFoundException;
use Novactive\EzMenuManager\Exception\UnexpectedTypeException;
use Novactive\EzMenuManager\MenuItem\MenuItemConverter;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Symfony\Component\Form\DataTransformerInterface;

class MenuItemsCollectionTransformer implements DataTransformerInterface
{
    /**
     * MenuItemsCollection constructor.
     */
    public function __construct(protected MenuItemConverter $menuItemConverter)
    {
    }

    /**
     * Transforms a FieldType Value into a hash using `FieldTpe::toHash()`.
     * This hash is compatible with `reverseTransform()`.
     *
     * @throws UnexpectedTypeException
     *
     * @return string|false the value's hash, or null if $value was not a FieldType Value
     */
    public function transform($value): mixed
    {
        return json_encode($this->menuItemConverter->toHashArray($value->getValues()));
    }

    /**
     * Transforms a hash into a FieldType Value using `FieldType::fromHash()`.
     * The FieldValue is compatible with `transform()`.
     *
     * @throws MenuItemTypeNotFoundException
     *
     * @return MenuItem[]
     */
    public function reverseTransform($value): array
    {
        return $this->menuItemConverter->fromHashArray(json_decode((string) $value, true));
    }
}
