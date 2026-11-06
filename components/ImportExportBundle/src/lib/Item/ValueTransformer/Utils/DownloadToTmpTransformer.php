<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\File\TempFileUtil;
use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;

/**
 * Downloads a file from a given URL and saves it to a temporary location.
 * Return the path to the temporary file.
 *
 * @see https://www.php.net/manual/en/function.tempnam.php
 */
class DownloadToTmpTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): ?string
    {
        if (empty($value)) {
            return null;
        }

        return TempFileUtil::download($value);
    }
}
