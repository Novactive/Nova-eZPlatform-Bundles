<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Ibexa\Contracts\FieldTypeRichText\RichText\InputHandlerInterface as RichTextInputHandlerInterface;
use Ibexa\FieldTypeRichText\FieldType\RichText\Value;

/**
 * Transform an HTML string to a RichText Value.
 */
class HtmlToRichtextTransformer extends AbstractItemValueTransformer
{
    public function __construct(
        protected RichTextInputHandlerInterface $richtextInputHandler
    ) {
    }

    protected function transform(mixed $value, array $options = []): Value
    {
        if (null === $value) {
            return new Value();
        }

        $convertedDoc = $this->richtextInputHandler->fromString(
            sprintf(
                '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://ibexa.co/namespaces/ezpublish5/xhtml5/edit">%s</section>',
                $value
            )
        );

        return new Value($convertedDoc);
    }
}
