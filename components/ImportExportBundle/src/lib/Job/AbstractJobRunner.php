<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

abstract class AbstractJobRunner implements JobRunnerInterface
{
    public function __invoke(Job $job, bool $force = false): void
    {
        if (!$force && (Job::STATUS_PENDING !== $job->getStatus() && Job::STATUS_COMPLETED !== $job->getStatus())) {
            return;
        }
        $job->reset();
        $this->run($job);
    }

    abstract protected function run(Job $job): void;
}
