<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\File;

use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReader;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @template TFileReaderOptions of FileReaderOptions
 * @extends  AbstractReader<TFileReaderOptions>
 */
abstract class AbstractFileReader extends AbstractReader
{
    /** @var resource|null */
    protected $tmpFile = null;

    public function __construct(
        protected FileHandler $fileHandler
    ) {
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     *
     * @return resource
     */
    protected function getFileStream()
    {
        $options = $this->getOptions();
        if ($options->file instanceof File) {
            return fopen($options->file->getRealPath(), 'rb');
        }

        return $this->fileHandler->readStream($options->file);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     *
     * @return resource
     */
    protected function getFileTmpCopy()
    {
        if (null === $this->tmpFile) {
            $this->tmpFile = tmpfile();
            $originalFile = $this->getFileStream();
            stream_copy_to_stream($originalFile, $this->tmpFile);
        }

        return $this->tmpFile;
    }

    public function clean(): void
    {
        $options = $this->getOptions();

        if (is_string($options->file)) {
            $this->fileHandler->delete($options->file);
        }
    }

    public static function getOptionsFormType(): ?string
    {
        return FileReaderOptionsFormType::class;
    }

    public static function getOptionsType(): string
    {
        return FileReaderOptions::class;
    }
}
