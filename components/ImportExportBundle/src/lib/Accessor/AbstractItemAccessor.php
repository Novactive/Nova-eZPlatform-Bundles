<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

abstract class AbstractItemAccessor implements ItemAccessorInterface
{
    public function getPropertyAccessor(): PropertyAccessorInterface
    {
        return PropertyAccess::createPropertyAccessorBuilder()
                                                ->getPropertyAccessor();
    }
}
