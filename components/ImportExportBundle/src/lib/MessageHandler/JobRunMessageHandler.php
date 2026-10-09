<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\MessageHandler;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRepository;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRunner;
use AlmaviaCX\Bundle\IbexaImportExport\Message\JobResumeMessage;
use AlmaviaCX\Bundle\IbexaImportExport\Message\JobRunMessage;
use AlmaviaCX\Bundle\IbexaImportExport\Message\JobStartMessage;
use AlmaviaCX\Bundle\IbexaImportExport\Notification\NotificationSender;
use Symfony\Component\Messenger\MessageBusInterface;

class JobRunMessageHandler
{
    public function __construct(
        protected ExecutionRepository $executionRepository,
        protected ExecutionRunner $executionRunner,
        protected NotificationSender $notificationSender,
        protected MessageBusInterface $messageBus
    ) {
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __invoke(JobRunMessage $message): void
    {
        $execution = $this->executionRepository->findById($message->getExecutionId());
        $status = ($this->executionRunner)($execution, $message->getBatchLimit());

        if (Execution::STATUS_COMPLETED === $status) {
            ($this->notificationSender)(
                $execution->getCreatorId(),
                NotificationSender::JOB_DONE_TYPE,
                [
                    'job_id' => $execution->getJob()->getId(),
                    'job_label' => $execution->getJob()->getLabel(),
                    'message' => NotificationSender::MESSAGES[NotificationSender::JOB_DONE_TYPE],
                    'message_parameters' => [
                        '%job_id%' => $execution->getJob()->getId(),
                        '%job_label%' => $execution->getJob()->getLabel(),
                    ],
                ]
            );
        }
        if (Execution::STATUS_PAUSED === $status) {
            $this->triggerResume($execution, $message->getBatchLimit());
        }
    }

    public function triggerStart(Execution $execution, int $batchLimit = -1): void
    {
        $this->messageBus->dispatch(new JobStartMessage($execution->getId(), $batchLimit));
    }

    public function triggerResume(Execution $execution, int $batchLimit = -1): void
    {
        $this->messageBus->dispatch(new JobResumeMessage($execution->getId(), $batchLimit));
    }
}
