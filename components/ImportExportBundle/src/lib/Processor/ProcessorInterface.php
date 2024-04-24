<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentInterface;

interface ProcessorInterface extends ComponentInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke($item);
}
