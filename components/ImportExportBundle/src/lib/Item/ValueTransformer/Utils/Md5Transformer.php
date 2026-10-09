<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;

/**
 * Transforms a value to its MD5 hash representation.
 */
class Md5Transformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): string
    {
        return md5($value);
    }
}
