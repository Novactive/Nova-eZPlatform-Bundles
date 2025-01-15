<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;

/**
 * Transforms a value to its string representation.
 */
class ToStringTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): string
    {
        return (string) $value;
    }
}
