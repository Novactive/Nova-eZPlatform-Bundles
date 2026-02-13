<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorInterface;

/**
 * @template TOptions of StepOptions
 * @extends ProcessorInterface<TOptions>
 */
interface StepInterface extends ProcessorInterface
{
}
