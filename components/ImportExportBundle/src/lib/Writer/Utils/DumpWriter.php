<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class DumpWriter extends AbstractWriter implements TranslationContainerInterface
{
    protected function writeItem($item, $mappedItem)
    {
        dd($item, $mappedItem);
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('writer.dump.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [(new Message('writer.dump.name', 'import_export'))->setDesc('Dump Writer')];
    }

    public static function getResultTemplate(): ?string
    {
        return null;
    }
}
