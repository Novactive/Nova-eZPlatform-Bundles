<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event\Subscriber;

use AlmaviaCX\Bundle\IbexaImportExport\Event\PostJobCreateFormSubmitEvent;
use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptions;
use League\Flysystem\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class PostJobCreateFormSubmitEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected FileHandler $fileHandler
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostJobCreateFormSubmitEvent::class => ['onPostJobCreateFormSubmit', 0],
        ];
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function onPostJobCreateFormSubmit(PostJobCreateFormSubmitEvent $event): void
    {
        $job = $event->getJob();

        $options = $job->getOptions()->readerOptions ?? null;
        if ($options instanceof FileReaderOptions && $options->file instanceof File) {
            $file = $options->file;
            $fileHandler = fopen($file->getPathname(), 'rb');
            $newFilename = sprintf(
                'job/file_reader_%s.%s',
                Uuid::v4(),
                $file instanceof UploadedFile ?
                    $file->getClientOriginalExtension() :
                    pathinfo($file->getFilename(), PATHINFO_EXTENSION)
            );
            $this->fileHandler->writeStream($newFilename, $fileHandler, new Config());
            $options->file = $newFilename;
        }
    }
}
