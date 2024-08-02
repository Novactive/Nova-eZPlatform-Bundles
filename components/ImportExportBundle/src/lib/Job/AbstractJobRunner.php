<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Event\ResetJobRunEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractJobRunner implements JobRunnerInterface
{
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Job $job, int $batchLimit = -1, bool $reset = false): int
    {
        if ($reset || Job::STATUS_COMPLETED === $job->getStatus()) {
            $this->eventDispatcher->dispatch(new ResetJobRunEvent($job));
            $job->reset();
        }

        if (Job::STATUS_PAUSED !== $job->getStatus() || Job::STATUS_PENDING !== $job->getStatus()) {
            return $this->run($job, $batchLimit);
        }

        return $job->getStatus();
    }

    abstract protected function run(Job $job, int $batchLimit = -1): int;
}
