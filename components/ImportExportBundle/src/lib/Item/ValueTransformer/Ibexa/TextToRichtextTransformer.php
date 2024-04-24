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
            $rawText = sprintf(
                '<p>%s</p>',
                $value
            );

            $rawText = str_replace(['&nbsp;'], [' '], $rawText);
            $rawText = preg_replace(['/\\n/'], [''], $rawText);
        }

        return ($this->htmlToRichtextTransformer)($rawText);
    }
}
