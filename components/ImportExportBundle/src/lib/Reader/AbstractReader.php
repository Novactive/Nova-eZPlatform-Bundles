<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

use AlmaviaCX\Bundle\IbexaImportExport\Component\AbstractComponent;

/**
 * @template TReaderOptions of ReaderOptions
 * @extends  AbstractComponent<TReaderOptions>
 * @implements ReaderInterface<TReaderOptions>
 */
abstract class AbstractReader extends AbstractComponent implements ReaderInterface
{
    public static function getOptionsType(): string
    {
        return ReaderOptions::class;
    }

    public static function getDetailsTemplate(): ?string
    {
        return null;
    }
}
