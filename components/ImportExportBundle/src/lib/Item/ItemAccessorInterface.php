<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

interface ItemAccessorInterface
{
    public function getPropertyAccessor(): PropertyAccessorInterface;
}
