<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event\Subscriber;

use AlmaviaCX\Bundle\IbexaImportExport\Event\ResetJobRunEvent;
use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\Csv\CsvWriter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RemoveWrittenFilesEventSubscriber implements EventSubscriberInterface
{
    protected FileHandler $fileHandler;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResetJobRunEvent::class => ['onResetJob', 0],
        ];
    }

    public function onResetJob(ResetJobRunEvent $event)
    {
        $job = $event->getJob();
        $results = $job->getWriterResults();

        foreach ($results as $result) {
            if (CsvWriter::class === $result->getWriterType() && isset($result->getResults()['filepath'])) {
                $this->fileHandler->delete($result->getResults()['filepath']);
            }
        }
    }
}
