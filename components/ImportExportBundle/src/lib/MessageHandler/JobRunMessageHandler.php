<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\MessageHandler;

use AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobRunnerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Message\JobRunMessage;
use AlmaviaCX\Bundle\IbexaImportExport\Notification\NotificationSender;

class JobRunMessageHandler
{
    protected JobRepository $jobRepository;
    protected JobRunnerInterface $jobRunner;
    protected NotificationSender $notificationSender;

    public function __construct(
        JobRepository $jobRepository,
        JobRunnerInterface $jobRunner,
        NotificationSender $notificationSender
    ) {
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
        ($this->jobRunner)($job, true);
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
}
