<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\File;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractFileReader<FileReaderOptions>
 */
class FileReader extends AbstractFileReader implements TranslationContainerInterface
{
    /**
     * @throws \League\Flysystem\FilesystemException
     *
     * @return FileReaderIterator
     */
    public function __invoke()
    {
        $stream = $this->getFileTmpCopy();

        return new FileReaderIterator(
            $stream
        );
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.file.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('reader.file.name', 'import_export') )->setDesc('File input')];
    }
}
