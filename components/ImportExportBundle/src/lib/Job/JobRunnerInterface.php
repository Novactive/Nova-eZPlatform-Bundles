<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;

interface JobRunnerInterface
{
    public function __invoke(Job $job, int $batchLimit = -1, bool $reset = false): int;

    public function runExecution(Execution $execution, int $batchLimit = -1): int;
}
