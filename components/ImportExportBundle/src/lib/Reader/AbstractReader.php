<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

use AlmaviaCX\Bundle\IbexaImportExport\Component\AbstractComponent;

abstract class AbstractReader extends AbstractComponent implements ReaderInterface
{
    public static function getOptionsType(): ?string
    {
        return ReaderOptions::class;
    }
}
