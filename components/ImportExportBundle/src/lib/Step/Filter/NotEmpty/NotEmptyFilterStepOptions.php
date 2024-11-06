<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Step\Filter\NotEmpty;

use AlmaviaCX\Bundle\IbexaImportExport\Step\StepOptions;

/**
 * @property string $propertyPath
 */
class NotEmptyFilterStepOptions extends StepOptions
{
    protected string $propertyPath;
}
