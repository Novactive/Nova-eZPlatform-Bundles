<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\CallbackIteratorItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\SeekableItemIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\AbstractFileReader;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class CsvReader extends AbstractFileReader implements TranslationContainerInterface
{
    protected SlugConverter $slugConverter;

    public function __construct(FileHandler $fileHandler, SlugConverter $slugConverter)
    {
        $this->slugConverter = $slugConverter;
        parent::__construct($fileHandler);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     *
     * @return \Iterator<\AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface>
     */
    public function __invoke(): ReaderIteratorInterface
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Reader\Csv\CsvReaderOptions $options */
        $options = $this->getOptions();

        $headerRowNumber = $options->headerRowNumber;
        $stream = $this->getFileStream();

        $firstRow = 0;
        $headers = [];
        if (null !== $headerRowNumber) {
            $firstRow = $headerRowNumber + 1;
        }

        $iterator = new CsvFileReadIterator(
            $stream,
            $firstRow,
            $options->delimiter,
            $options->enclosure,
            $options->escape,
        );

        $totalLines = $iterator->count();
        if (null !== $headerRowNumber) {
            $iterator->seek($headerRowNumber);
            $headers = array_map(function ($header) {
                return $this->cleanHeader($header);
            }, $iterator->current());
            $iterator->rewind();
            $totalLines -= $headerRowNumber;
        }

        return new SeekableItemIterator(
            $totalLines,
            $iterator,
            new CallbackIteratorItemTransformer(function ($item) use ($headers) {
                return $this->transformItem(
                    new ArrayAccessor(!empty($headers) ? array_combine($headers, $item) : $item)
                );
            })
        );
    }

    protected function transformItem($item)
    {
        return $item;
    }

    protected function cleanHeader(string $value): string
    {
        return $this->slugConverter->convert(trim($value));
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.csv.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('reader.csv.name', 'import_export') )->setDesc('CSV Reader')];
    }

    public static function getOptionsFormType(): ?string
    {
        return CsvReaderOptionsFormType::class;
    }

    public static function getOptionsType(): ?string
    {
        return CsvReaderOptions::class;
    }
}
