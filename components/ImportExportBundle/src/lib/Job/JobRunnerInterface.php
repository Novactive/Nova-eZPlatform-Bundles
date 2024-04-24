<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

interface JobRunnerInterface
{
    public function __invoke(Job $job, bool $force = false): void;
}
