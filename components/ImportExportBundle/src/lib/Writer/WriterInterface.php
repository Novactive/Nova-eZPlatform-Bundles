<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;

interface WriterInterface extends ProcessorInterface
{
    public function prepare(): void;

    public function finish(): WriterResults;

    public static function getResultTemplate(): ?string;
}
