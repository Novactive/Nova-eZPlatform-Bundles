<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Ibexa\FieldTypeRichText\FieldType\RichText\Value;

/**
 * Transforms a text value to its richtext representation.
 */
class TextToRichtextTransformer extends AbstractItemValueTransformer
{
    public function __construct(
        protected HtmlToRichtextTransformer $htmlToRichtextTransformer
    ) {
    }

    protected function transform(mixed $value, array $options = []): Value
    {
        $rawText = null;
        if ($value) {
            if (is_scalar($value)) {
                $value = [$value];
            }

            $rawText = [];
            foreach ($value as $text) {
                $rawText[] = sprintf(
                    '<p>%s</p>',
                    nl2br(htmlentities(trim((string) $text)))
                );
            }

            $rawText = str_replace(['&nbsp;'], [' '], implode(PHP_EOL, $rawText));
            $rawText = preg_replace('/[\n\r]/', '', $rawText);
        }

        return ($this->htmlToRichtextTransformer)($rawText);
    }
}
