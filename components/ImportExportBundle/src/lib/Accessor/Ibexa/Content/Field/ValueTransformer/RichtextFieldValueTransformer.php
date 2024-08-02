<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter as RichTextConverterInterface;

class RichtextFieldValueTransformer implements FieldValueTransformerInterface
{
    protected RichTextConverterInterface $richTextOutputConverter;

    public function __construct(
        RichTextConverterInterface $richTextOutputConverter
    ) {
        $this->richTextOutputConverter = $richTextOutputConverter;
    }

    public function __invoke(Field $field, FieldDefinition $fieldDefinition): object
    {
        /** @var \Ibexa\FieldTypeRichText\FieldType\RichText\Value $fieldValue */
        $fieldValue = $field->value;

        return (object) [
            'xml' => $fieldValue->xml->saveXML(),
            'html' => $this->richTextOutputConverter->convert($fieldValue->xml)->saveHTML(),
        ];
    }
}
