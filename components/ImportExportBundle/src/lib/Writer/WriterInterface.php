<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;

/**
 * @template TWriterOptions of WriterOptions
 * @extends ProcessorInterface<TWriterOptions>
 */
interface WriterInterface extends ProcessorInterface
{
    public static function getResultTemplate(): ?string;
}
