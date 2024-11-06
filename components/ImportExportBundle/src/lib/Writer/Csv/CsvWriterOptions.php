<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Csv;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream\StreamWriterOptions;

/**
 * @property string $delimiter
 * @property string $enclosure
 * @property bool   $utf8Encode
 * @property bool   $prependHeaderRow
 */
class CsvWriterOptions extends StreamWriterOptions
{
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected bool $utf8Encode = false;
    protected bool $prependHeaderRow = false;
}
