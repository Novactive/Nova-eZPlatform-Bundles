<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Mdb;

use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\DoctrineSeekableItemIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\AbstractFileReader;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use JMS\TranslationBundle\Model\Message;
use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Translation\TranslatableMessage;

class MdbReader extends AbstractFileReader
{
    protected string $converterPath;
    /** @var resource */
    protected $dbFile;
    protected Connection $connection;

    public function __construct(
        FileHandler $fileHandler,
        string $converterPath = __DIR__.'/../../../../bin/mdb-to-sqlite.bash'
    ) {
        $this->converterPath = $converterPath;
        parent::__construct($fileHandler);
    }

    public function prepare(): void
    {
        parent::prepare();

        $filePath = $this->getFileTmpCopy();

        $this->dbFile = tmpfile();
        $tmpFileMetadata = stream_get_meta_data($this->dbFile);

        $process = new Process(
            [
                $this->converterPath,
                $filePath,
                $tmpFileMetadata['uri'],
            ]
        );
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                sprintf('An error occurred while converting the mdb file: %s', $process->getErrorOutput())
            );
        }

        $this->connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $tmpFileMetadata['uri']]);
    }

    public function __invoke(): ReaderIteratorInterface
    {
        /** @var MdbReaderOptions $options */
        $options = $this->getOptions();

        return new DoctrineSeekableItemIterator(
            $this->connection,
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

    public static function getOptionsType(): ?string
    {
        return MdbReaderOptions::class;
    }

    public static function getOptionsFormType(): ?string
    {
        return MdbReaderOptionsFormType::class;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
