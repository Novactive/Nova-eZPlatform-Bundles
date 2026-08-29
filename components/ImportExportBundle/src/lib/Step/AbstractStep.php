<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;

/**
 * @template TOptions of StepOptions
 * @extends AbstractProcessor<TOptions>
 * @implements StepInterface<TOptions>
 */
abstract class AbstractStep extends AbstractProcessor implements StepInterface
{
    public static function getOptionsType(): string
    {
        return StepOptions::class;
    }
}
