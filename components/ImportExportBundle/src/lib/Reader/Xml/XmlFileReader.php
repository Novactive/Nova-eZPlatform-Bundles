<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Xml;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\File\AbstractFileReader;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractFileReader<XmlFileReaderOptions>
 */
class XmlFileReader extends AbstractFileReader implements TranslationContainerInterface
{
    /**
     * @throws \League\Flysystem\FilesystemException
     *
     * @return XmlFileReaderIterator
     */
    public function __invoke()
    {
        $options = $this->getOptions();

        return new XmlFileReaderIterator($this->getFileTmpCopy(), $options->nodeNameSelector);
    }

    public static function getOptionsFormType(): ?string
    {
        return XmlFileReaderOptionsFormType::class;
    }

    public static function getOptionsType(): string
    {
        return XmlFileReaderOptions::class;
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
