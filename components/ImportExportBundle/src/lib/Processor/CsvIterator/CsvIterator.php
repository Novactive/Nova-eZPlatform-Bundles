<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Processor\CsvIterator;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Processor\AbstractProcessor;
use Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * This processor is used to transform a CSV line into an array.
 *
 * @extends AbstractProcessor<CsvIteratorOptions>
 */
class CsvIterator extends AbstractProcessor implements TranslationContainerInterface
{
    public function __construct(
        protected SlugConverter $slugConverter,
        protected SourceResolver $sourceResolver
    ) {
    }

    public function processItem($item): mixed
    {
        $options = $this->getOptions();
        $csvLine = ($this->sourceResolver)($options->source, $item, $this->getReferenceBag());

        return new ArrayAccessor(
            str_getcsv(
                $csvLine,
                $options->delimiter,
                $options->enclosure,
                $options->escape
            )
        );
    }

    public static function getName(): TranslatableMessage|string
    {
        return new TranslatableMessage(/* @Desc("CSV Iterator") */ 'processor.csv_iterator.name');
    }

    public static function getTranslationMessages(): array
    {
        return [(new Message('processor.csv_iterator.name'))->setDesc('CSV Iterator')];
    }

    public static function getOptionsFormType(): ?string
    {
        return CsvIteratorOptionsFormType::class;
    }

    public static function getOptionsType(): string
    {
        return CsvIteratorOptions::class;
    }
}
