<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Mdb;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptions;

/**
 * @property string $queryString
 * @property string $countQueryString
 */
class MdbReaderOptions extends FileReaderOptions
{
    protected string $queryString;
    protected string $countQueryString;
}
