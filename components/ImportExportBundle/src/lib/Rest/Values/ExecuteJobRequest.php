<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Rest\Values;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;

class ExecuteJobRequest
{
    public function __construct(
        public Job $job,
        public ExecutionOptions $executionOptions,
        public ?int $batchLimit = null
    ) {
    }
}
