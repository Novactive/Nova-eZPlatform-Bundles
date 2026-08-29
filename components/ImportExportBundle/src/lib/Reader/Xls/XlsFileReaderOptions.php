<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xls;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptions;

/**
 * @property ?string                               $tabName
 * @property int                                   $headerRowNumber
 * @property array{'start': string, 'end': string} $colsRange
 */
class XlsFileReaderOptions extends FileReaderOptions
{
    protected ?string $tabName;
    protected ?int $headerRowNumber = null;
    /** @var array{'start': string, 'end': ?string} */
    protected array $colsRange = ['start' => 'A', 'end' => null];
}
