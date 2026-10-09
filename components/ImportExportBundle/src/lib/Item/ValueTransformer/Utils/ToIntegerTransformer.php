<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;

/**
 * Transforms a value to its integer representation.
 */
class ToIntegerTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): int
    {
        return intval($value);
    }
}
