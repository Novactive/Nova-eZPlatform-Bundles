<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;

class ToIntegerTransformer extends AbstractItemValueTransformer
{
    protected function transform($value, array $options = [])
    {
        return intval($value);
    }
}
