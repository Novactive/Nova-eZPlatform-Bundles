<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Notification;

use Ibexa\Contracts\Core\Repository\NotificationService;
use Ibexa\Contracts\Core\Repository\Values\Notification\CreateStruct;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class NotificationSender implements TranslationContainerInterface
{
    public const JOB_DONE_TYPE = 'import_export/notification/job_done';

    public const MESSAGES = [
        self::JOB_DONE_TYPE => 'notification.job.done',
    ];

    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __invoke(int $receiverId, string $type, array $data = []): void
    {
        $notification = new CreateStruct();
        $notification->ownerId = $receiverId;
        $notification->type = 'import_export:notification:default';
        $notification->data = $data;

        $this->notificationService->createNotification($notification);
    }

    public static function getTranslationMessages(): array
    {
        return [
            ( new Message(self::MESSAGES[self::JOB_DONE_TYPE], 'import_export') )
                ->setDesc('Job %job_label% (%job_id%) is done running.'),
        ];
    }
}
