<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step\Callback;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Step\StepOptions;

/**
 * @property callable(object|array $item): ?ItemAccessorInterface $callback
 */
class CallbackStepOptions extends StepOptions
{
    /** @var callable(object|array): ?ItemAccessorInterface */
    protected $callback;
}
