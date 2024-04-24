<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream;

use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterResults;
use League\Flysystem\Config;

abstract class AbstractStreamWriter extends AbstractWriter
{
    /**
     * @var resource
     */
    protected $stream;
    protected FileHandler $fileHandler;

    public function __construct(
        FileHandler $fileHandler,
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
        ReferenceBag $references
    ) {
        $this->fileHandler = $fileHandler;
        parent::__construct($sourceResolver, $itemTransformer, $references);
    }

    public function prepare(): void
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'import_export_writer_');
        $this->stream = fopen($tmpName, 'w+');
    }

    public function finish(): WriterResults
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream\StreamWriterOptions $options */
        $options = $this->getOptions();
        $filepath = ($this->fileHandler)->resolvePath($options->filepath);

        rewind($this->stream);
        $this->fileHandler->writeStream($filepath, $this->stream, new Config());

        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        return new WriterResults(static::class, ['filepath' => $filepath]);
    }

    public static function getOptionsFormType(): ?string
    {
        return StreamWriterOptionsFormType::class;
    }

    public static function getOptionsType(): ?string
    {
        return StreamWriterOptions::class;
    }
}
