<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;

class TextToRichtextTransformer extends AbstractItemValueTransformer
{
    protected HtmlToRichtextTransformer $htmlToRichtextTransformer;

    public function __construct(
        HtmlToRichtextTransformer $htmlToRichtextTransformer
    ) {
        $this->htmlToRichtextTransformer = $htmlToRichtextTransformer;
    }

    public function transform($value, array $options = [])
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
