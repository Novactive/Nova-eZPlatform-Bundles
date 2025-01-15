<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step\Callback;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use AlmaviaCX\Bundle\IbexaImportExport\Step\StepOptions;

/**
 * @property callable(object|array<mixed, mixed> $item, ReferenceBag $referenceBag): ?ItemAccessorInterface $callback
 */
class CallbackStepOptions extends StepOptions
{
    /**
     * @var callable(object|array<mixed, mixed>, ReferenceBag): ?ItemAccessorInterface
     */
    protected $callback;
}
