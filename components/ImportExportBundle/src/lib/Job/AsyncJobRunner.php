<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRepository;
use AlmaviaCX\Bundle\IbexaImportExport\MessageHandler\JobRunMessageHandler;

class AsyncJobRunner extends AbstractJobRunner
{
    public function __construct(
        protected JobRunMessageHandler $jobRunMessageHandler,
        JobRepository $jobRepository,
        ExecutionRepository $executionRepository,
    ) {
        parent::__construct($jobRepository, $executionRepository);
    }

    public function runExecution(Execution $execution, int $batchLimit = -1): int
    {
        if (Execution::STATUS_PAUSED === $execution->getStatus()) {
            $this->jobRunMessageHandler->triggerResume($execution, $batchLimit);
        } elseif ($execution->canRun()) {
            $execution->setStatus(Execution::STATUS_QUEUED);
            $this->executionRepository->save($execution);

            $this->jobRunMessageHandler->triggerStart($execution, $batchLimit);
        }

        return $execution->getStatus();
    }
}
