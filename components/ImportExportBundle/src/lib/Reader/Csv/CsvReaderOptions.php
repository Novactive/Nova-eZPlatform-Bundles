<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptions;

/**
 * @property ?int   $headerRowNumber
 * @property bool   $strict
 * @property string $delimiter
 * @property string $enclosure
 * @property string $escape
 */
class CsvReaderOptions extends FileReaderOptions
{
    public const DELIMITERS = [','];
    public const ENCLOSURE = ['"'];
    public const ESCAPE = ['\\'];

    protected ?int $headerRowNumber = null;
    protected bool $strict = true;
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escape = '\\';
}
