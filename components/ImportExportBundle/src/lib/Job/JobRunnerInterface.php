<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

interface JobRunnerInterface
{
    public function __invoke(Job $job, int $batchLimit = -1, bool $reset = false): int;
}
