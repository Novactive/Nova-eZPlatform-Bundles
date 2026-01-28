<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Utils;

class Checksum
{
    public string $identifier = 'import_checksum';
    public string|null $value = null;
}
