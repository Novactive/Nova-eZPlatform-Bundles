<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Message\JobRunMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class AsyncJobRunner extends AbstractJobRunner
{
    protected MessageBusInterface $messageBus;
    protected JobRepository $jobRepository;

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository $jobRepository
     */
    public function __construct(MessageBusInterface $messageBus, JobRepository $jobRepository)
    {
        $this->messageBus = $messageBus;
        $this->jobRepository = $jobRepository;
    }

    protected function run(Job $job): void
    {
        $job->setStatus(Job::STATUS_QUEUED);
        $this->jobRepository->save($job);

        $this->messageBus->dispatch(new JobRunMessage($job->getId()));
    }
}
