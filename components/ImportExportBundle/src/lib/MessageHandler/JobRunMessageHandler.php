<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\MessageHandler;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobRunnerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Message\JobResumeMessage;
use AlmaviaCX\Bundle\IbexaImportExport\Message\JobRunMessage;
use AlmaviaCX\Bundle\IbexaImportExport\Message\JobStartMessage;
use AlmaviaCX\Bundle\IbexaImportExport\Notification\NotificationSender;
use Symfony\Component\Messenger\MessageBusInterface;

class JobRunMessageHandler
{
    protected JobRepository $jobRepository;
    protected JobRunnerInterface $jobRunner;
    protected NotificationSender $notificationSender;
    protected MessageBusInterface $messageBus;

    public function __construct(
        JobRepository $jobRepository,
        JobRunnerInterface $jobRunner,
        NotificationSender $notificationSender,
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
        $this->notificationSender = $notificationSender;
        $this->jobRepository = $jobRepository;
        $this->jobRunner = $jobRunner;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __invoke(JobRunMessage $message): void
    {
        $job = $this->jobRepository->findById($message->getJobId());
        $status = ($this->jobRunner)($job, $message->getBatchLimit());

        if (Job::STATUS_COMPLETED === $status) {
            ($this->notificationSender)(
                $job->getCreatorId(),
                NotificationSender::JOB_DONE_TYPE,
                [
                    'job_id' => $job->getId(),
                    'job_label' => $job->getLabel(),
                    'message' => NotificationSender::MESSAGES[NotificationSender::JOB_DONE_TYPE],
                    'message_parameters' => [
                        '%job_id%' => $job->getId(),
                        '%job_label%' => $job->getLabel(),
                    ],
                ]
            );
        }
        if (Job::STATUS_PAUSED === $status) {
            $this->triggerResume($job, $message->getBatchLimit());
        }
    }

    public function triggerStart(Job $job, int $batchLimit = -1): void
    {
        $this->messageBus->dispatch(new JobStartMessage($job->getId(), $batchLimit));
    }

    public function triggerResume(Job $job, int $batchLimit = -1): void
    {
        $this->messageBus->dispatch(new JobResumeMessage($job->getId(), $batchLimit));
    }
}
