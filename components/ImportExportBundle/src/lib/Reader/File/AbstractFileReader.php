<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\File;

use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReader;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractFileReader extends AbstractReader
{
    protected FileHandler $fileHandler;
    /** @var resource|null */
    protected $tmpFile = null;

    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    protected function getFileStream()
    {
        /** @var FileReaderOptions $options */
        $options = $this->getOptions();
        if ($options->file instanceof File) {
            return fopen($options->file->getRealPath(), 'rb');
        }

        return $this->fileHandler->readStream($options->file);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    protected function getFileTmpCopy(): string
    {
        if (null === $this->tmpFile) {
            $this->tmpFile = tmpfile();
            $originalFile = $this->getFileStream();
            stream_copy_to_stream($originalFile, $this->tmpFile);
        }

        $tmpFileMetadata = stream_get_meta_data($this->tmpFile);

        return $tmpFileMetadata['uri'];
    }

    public static function getOptionsFormType(): ?string
    {
        return FileReaderOptionsFormType::class;
    }

    public static function getOptionsType(): ?string
    {
        return FileReaderOptions::class;
    }

    public function clean(): void
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Reader\File\FileReaderOptions $options */
        $options = $this->getOptions();

        if (is_string($options->file)) {
            $this->fileHandler->delete($options->file);
        }
    }
}
