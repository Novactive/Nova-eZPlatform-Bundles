<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\Source;

class Checksum
{
    public string $identifier = 'import_checksum';
    public string|null $value = null;
}
