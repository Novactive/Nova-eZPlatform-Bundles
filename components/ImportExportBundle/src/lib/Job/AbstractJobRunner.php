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
        if ($reset || in_array($job->getStatus(), [Job::STATUS_COMPLETED, Job::STATUS_CANCELED])) {
            $this->eventDispatcher->dispatch(new ResetJobRunEvent($job));
            $job->reset();
        }

        if (!$job->canRun()) {
            return $job->getStatus();
        }

        return $this->run($job, $batchLimit);
    }

    abstract protected function run(Job $job, int $batchLimit = -1): int;
}
