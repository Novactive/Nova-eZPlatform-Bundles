<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;

class ResetJobRunEvent
{
    public function __construct(
        protected Execution $execution
    ) {
    }

    public function getExecution(): Execution
    {
        return $this->execution;
    }
}
