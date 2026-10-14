<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;

class ChecksumTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): string
    {
        return hash('sha256', json_encode($value));
    }
}
