<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream;

use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use League\Flysystem\Config;
use League\Flysystem\FilesystemException;

/**
 * @template TOptions of StreamWriterOptions
 * @extends AbstractWriter<TOptions>
 */
abstract class AbstractStreamWriter extends AbstractWriter
{
    public const MODE_NEW_FILE = 'new';
    public const MODE_APPEND_FILE = 'append';
    /**
     * @var resource
     */
    protected $stream;
    protected string $mode = self::MODE_NEW_FILE;

    public function __construct(
        protected FileHandler $fileHandler,
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
    ) {
        parent::__construct($sourceResolver, $itemTransformer);
    }

    public function prepare(): void
    {
        $this->stream = fopen('php://temp', 'w+');
        $filepath = $this->results->getResult('filepath');
        if (!$filepath) {
            /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream\StreamWriterOptions $options */
            $options = $this->getOptions();
            $filepath = ($this->fileHandler)->resolvePath($options->filepath);
            $this->results->setResult('filepath', $filepath);
        } else {
            try {
                $existingStream = $this->fileHandler->readStream($filepath);
                stream_copy_to_stream($existingStream, $this->stream);
                $this->mode = self::MODE_APPEND_FILE;
            } catch (FilesystemException $e) {
                $this->logger->logException($e);
            }
        }
    }

    public function finish(): void
    {
        parent::finish();

        rewind($this->stream);
        $filepath = $this->results->getResult('filepath');
        $this->fileHandler->writeStream($filepath, $this->stream, new Config());

        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    public static function getOptionsFormType(): ?string
    {
        return StreamWriterOptionsFormType::class;
    }

    public static function getOptionsType(): string
    {
        return StreamWriterOptions::class;
    }
}
