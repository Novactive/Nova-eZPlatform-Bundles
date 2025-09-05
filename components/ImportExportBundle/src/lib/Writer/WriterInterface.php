<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;

interface WriterInterface extends ProcessorInterface
{
    public function setResults(WriterResults $results): void;

    public function getResults(): WriterResults;

    public static function getResultTemplate(): ?string;
}
