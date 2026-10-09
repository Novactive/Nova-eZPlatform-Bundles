<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Json;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\ArrayIteratorItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\SeekableItemIterator;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReader;
use ArrayIterator;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractReader<JsonReaderOptions>
 */
class JsonReader extends AbstractReader implements TranslationContainerInterface
{
    public function __invoke()
    {
        $json = $this->getOption('json', []);

        return new SeekableItemIterator(
            count($json),
            new ArrayIterator($json),
            new ArrayIteratorItemTransformer()
        );
    }

    public static function getOptionsType(): string
    {
        return JsonReaderOptions::class;
    }

    public static function getOptionsFormType(): ?string
    {
        return JsonReaderOptionsFormType::class;
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.json.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('reader.json.name', 'import_export') )->setDesc('Json input')];
    }

    public static function getDetailsTemplate(): ?string
    {
        return '@ibexadesign/import_export/reader/details/json.html.twig';
    }
}
