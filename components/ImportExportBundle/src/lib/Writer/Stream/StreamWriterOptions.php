<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptions;

/**
 * @property string $filepath
 */
class StreamWriterOptions extends WriterOptions
{
    protected string $filepath;
}
