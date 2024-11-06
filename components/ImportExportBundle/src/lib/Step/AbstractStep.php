<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;

abstract class AbstractStep extends AbstractProcessor implements StepInterface
{
    public static function getOptionsType(): ?string
    {
        return StepOptions::class;
    }
}
