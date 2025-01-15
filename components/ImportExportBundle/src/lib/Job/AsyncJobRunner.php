<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\MessageHandler\JobRunMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AsyncJobRunner extends AbstractJobRunner
{
    protected JobRunMessageHandler $jobRunMessageHandler;
    protected JobRepository $jobRepository;

    public function __construct(
        JobRunMessageHandler $jobRunMessageHandler,
        JobRepository $jobRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->jobRepository = $jobRepository;
        $this->jobRunMessageHandler = $jobRunMessageHandler;
        parent::__construct($eventDispatcher);
    }

    protected function run(Job $job, int $batchLimit = -1): int
    {
        $job->setStatus(Job::STATUS_QUEUED);
        $this->jobRepository->save($job);

        $this->jobRunMessageHandler->triggerStart($job, $batchLimit);

        return $job->getStatus();
    }
}
