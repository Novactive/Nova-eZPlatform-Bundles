<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step\Filter\Unique;

use AlmaviaCX\Bundle\IbexaImportExport\Step\StepOptions;

/**
 * @property string $propertyPath
 */
class UniqueFilterStepOptions extends StepOptions
{
    protected string $propertyPath;
}
