<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xml;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\CallbackIteratorItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\SeekableItemIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\AbstractFileReader;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class XmlReader extends AbstractFileReader implements TranslationContainerInterface
{
    public function __invoke(): ReaderIteratorInterface
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Reader\Xml\XmlReaderOptions $options */
        $options = $this->getOptions();

        $iterator = new XmlReaderIterator($this->getFileStream(), $options->nodeNameSelector);

        return new SeekableItemIterator(
            $iterator->count(),
            $iterator,
            new CallbackIteratorItemTransformer([$this, 'transformItem'])
        );
    }

    public function transformItem($item)
    {
        return $item;
    }

    public static function getOptionsFormType(): ?string
    {
        return XmlReaderOptionsFormType::class;
    }

    public static function getOptionsType(): ?string
    {
        return XmlReaderOptions::class;
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.xml.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('reader.xml.name', 'import_export') )->setDesc('XML Reader')];
    }
}
