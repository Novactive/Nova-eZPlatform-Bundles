<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Mdb;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\CallbackIteratorItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\DoctrineSeekableItemIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\AbstractFileReader;
use Doctrine\DBAL\DriverManager;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @phpstan-type ItemTransformer CallbackIteratorItemTransformer<array<string, mixed>, ArrayAccessor<string, mixed>>
 * @extends AbstractFileReader<MdbFileReaderOptions>
 */
class MdbFileReader extends AbstractFileReader implements TranslationContainerInterface
{
    /** @var resource */
    protected $dbFile;

    public function __construct(
        protected FileHandler $fileHandler,
        protected string $converterPath = __DIR__.'/../../../../bin/mdb-to-sqlite.bash',
        protected int $converterTimeout = 120,
    ) {
        parent::__construct($fileHandler);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \League\Flysystem\FilesystemException
     *
     * @return DoctrineSeekableItemIterator
     */
    public function __invoke()
    {
        $options = $this->getOptions();

        $this->dbFile = tmpfile();
        $tmpFileMetadata = stream_get_meta_data($this->dbFile);
        $fileMetadata = stream_get_meta_data($this->getFileTmpCopy());

        $process = new Process(
            [
                $this->converterPath,
                $fileMetadata['uri'],
                $tmpFileMetadata['uri'],
            ]
        );
        $process->setTimeout($this->converterTimeout);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                sprintf('An error occurred while converting the mdb file: %s', $process->getErrorOutput())
            );
        }

        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $tmpFileMetadata['uri']]);

        return new DoctrineSeekableItemIterator(
            $connection,
            $options->queryString,
            $options->countQueryString
        );
    }

    public function finish(): void
    {
        parent::finish();
        fclose($this->dbFile);
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.mdb.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('reader.mdb.name', 'import_export') )->setDesc('Microsoft Access Database reader')];
    }

    public static function getOptionsType(): string
    {
        return MdbFileReaderOptions::class;
    }

    public static function getOptionsFormType(): ?string
    {
        return MdbFileReaderOptionsFormType::class;
    }
}
