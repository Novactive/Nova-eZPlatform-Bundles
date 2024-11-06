<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Ibexa\Core\FieldType\TextBlock\Value;

class HtmlToTextBlockTransformer extends AbstractItemValueTransformer
{
    public function transform($value, array $options = [])
    {
        if (null === $value) {
            return new Value();
        }

        // Define block-level tags
        $blockTags = ['p'];

        // Add line breaks after block-level closing tags
        foreach ($blockTags as $tag) {
            $value = preg_replace('/<\/'.$tag.'>/', "</$tag>\n", $value);
        }

        // Replace <br> tags with newlines
        $value = preg_replace('/<br\s*\/?>/i', "\n", $value);

        // Strip HTML tags
        $text = strip_tags($value);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Trim leading and trailing whitespace/newlines
        $text = trim($text);

        return new Value($text);
    }
}
