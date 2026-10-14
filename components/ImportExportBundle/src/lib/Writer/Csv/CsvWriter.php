<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Csv;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream\AbstractStreamWriter;
use InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractStreamWriter<CsvWriterOptions>
 */
class CsvWriter extends AbstractStreamWriter implements TranslationContainerInterface
{
    private int $row = 0;

    public function prepare(): void
    {
        parent::prepare();
    }

    protected function writeItem($item, $mappedItem)
    {
        /** @var CsvWriterOptions $options */
        $options = $this->getOptions();
        if (self::MODE_NEW_FILE === $this->mode && $options->prependHeaderRow && 0 == $this->row++) {
            $headers = array_keys($mappedItem);
            fputcsv($this->stream, $headers, $options->delimiter, $options->enclosure);
        }

        if (!is_array($mappedItem)) {
            throw new InvalidArgumentException('[CsvWriter] provided item must be an array.');
        }
        foreach ($mappedItem as $valueIdentifier => $value) {
            if (!is_scalar($value) && !is_null($value)) {
                throw new InvalidArgumentException(
                    sprintf(
                        '[CsvWriter] provided value for "%s" must be scalar instead of %s.',
                        $valueIdentifier,
                        gettype($value)
                    )
                );
            }
        }

        fputcsv(
            $this->stream,
            $mappedItem,
            $options->delimiter,
            $options->enclosure
        );

        return $mappedItem;
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('writer.csv.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [(new Message('writer.csv.name', 'import_export'))->setDesc('CSV Writer')];
    }

    public static function getOptionsType(): string
    {
        return CsvWriterOptions::class;
    }

    public static function getResultTemplate(): ?string
    {
        return '@ibexadesign/import_export/writer/results/writer_csv.html.twig';
    }
}
