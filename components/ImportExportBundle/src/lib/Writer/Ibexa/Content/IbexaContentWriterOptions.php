<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptions;

/**
 * @property bool $allowUpdate
 */
class IbexaContentWriterOptions extends WriterOptions
{
    protected bool $allowUpdate = true;
}
