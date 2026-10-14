<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRepository;

abstract class AbstractJobRunner implements JobRunnerInterface
{
    public function __construct(
        protected JobRepository $jobRepository,
        protected ExecutionRepository $executionRepository
    ) {
    }

    public function __invoke(Job $job, int $batchLimit = -1, bool $reset = false): int
    {
        $execution = $job->getLastExecution();
        if ($reset && $execution) {
            $execution->setStatus(Execution::STATUS_CANCELED);
            $this->executionRepository->save($execution);
        }

        if (!$execution || $execution->isDone()) {
            $execution = new Execution();
            $job->addExecution($execution);
            $this->jobRepository->save($job);
        }

        if (!$execution->canRun()) {
            return $execution->getStatus();
        }

        return $this->runExecution($execution, $batchLimit);
    }

    abstract public function runExecution(Execution $execution, int $batchLimit = -1): int;
}
