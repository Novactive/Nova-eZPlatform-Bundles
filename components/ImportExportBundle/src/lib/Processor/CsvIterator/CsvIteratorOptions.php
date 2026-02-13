<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\CsvIterator;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\Source;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;

/**
 * @property ?int          $headerRowNumber
 * @property bool          $strict
 * @property string        $delimiter
 * @property string        $enclosure
 * @property string        $escape
 * @property Source|string $source
 */
class CsvIteratorOptions extends ProcessorOptions
{
    public const DELIMITERS = [','];
    public const ENCLOSURE = ['"'];
    public const ESCAPE = ['\\'];

    protected ?int $headerRowNumber = null;
    protected bool $strict = true;
    protected string $delimiter = ',';
    protected string $enclosure = '"';
    protected string $escape = '\\';
    protected Source|string $source;
}
