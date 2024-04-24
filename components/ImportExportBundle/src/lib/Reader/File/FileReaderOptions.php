<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\File;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @property string|File $file
 */
class FileReaderOptions extends ReaderOptions
{
    /** @var string|File */
    protected $file;
}
