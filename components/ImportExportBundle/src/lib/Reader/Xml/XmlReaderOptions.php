<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xml;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptions;

/**
 * @property string $nodeNameSelector;
 */
class XmlReaderOptions extends FileReaderOptions
{
    protected string $nodeNameSelector;
}
