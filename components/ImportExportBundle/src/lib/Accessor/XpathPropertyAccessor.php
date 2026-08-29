<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor;

use DOMNode;
use DOMXPath;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class XpathPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @param object|array<mixed>          $objectOrArray The object or array to modify
     * @param string|PropertyPathInterface $propertyPath  The property path to modify
     * @param mixed                        $value         The value to set at the end of the property path
     */
    public function setValue(&$objectOrArray, $propertyPath, $value): void
    {
        return;
    }

    /**
     * @param object|array<mixed>          $objectOrArray The object or array to traverse
     * @param string|PropertyPathInterface $propertyPath  The property path to read
     *
     * @throws \Symfony\Component\PropertyAccess\Exception\InvalidArgumentException
     * @throws \Symfony\Component\PropertyAccess\Exception\AccessException
     * @throws \Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        if (!$objectOrArray instanceof DOMNode) {
            return;
        }

        $xpath = new DOMXPath($objectOrArray->ownerDocument);

        return $xpath->evaluate((string) $propertyPath, $objectOrArray);
    }

    /**
     * @param object|array<mixed>          $objectOrArray The object or array to check
     * @param string|PropertyPathInterface $propertyPath  The property path to check
     */
    public function isWritable($objectOrArray, $propertyPath): bool
    {
        return false;
    }

    /**
     * @param object|array<mixed>          $objectOrArray The object or array to check
     * @param string|PropertyPathInterface $propertyPath  The property path to check
     */
    public function isReadable($objectOrArray, $propertyPath): bool
    {
        return true;
    }
}
