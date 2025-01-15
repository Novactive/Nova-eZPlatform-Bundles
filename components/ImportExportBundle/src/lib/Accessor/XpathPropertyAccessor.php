<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor;

use DOMNode;
use DOMXPath;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class XpathPropertyAccessor implements PropertyAccessorInterface
{
    public function setValue(&$objectOrArray, $propertyPath, $value)
    {
        return;
    }

    public function getValue($objectOrArray, $propertyPath)
    {
        if (!$objectOrArray instanceof DOMNode) {
            return;
        }

        $xpath = new DOMXPath($objectOrArray->ownerDocument);

        return $xpath->evaluate((string) $propertyPath, $objectOrArray);
    }

    public function isWritable($objectOrArray, $propertyPath)
    {
        return false;
    }

    public function isReadable($objectOrArray, $propertyPath)
    {
        return true;
    }
}
