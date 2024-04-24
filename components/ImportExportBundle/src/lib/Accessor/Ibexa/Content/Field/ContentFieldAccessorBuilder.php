<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer\FieldValueTransformerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

class ContentFieldAccessorBuilder
{
    /**
     * @var FieldValueTransformerInterface[]
     */
    protected array $contentFieldValueTransformers;

    /**
     * @param iterable<string, FieldValueTransformerInterface> $transformers
     */
    public function __construct(
        iterable $transformers
    ) {
        foreach ($transformers as $type => $transformer) {
            $this->contentFieldValueTransformers[$type] = $transformer;
        }
    }

    public function build(Field $field, FieldDefinition $fieldDefinition): ContentFieldAccessor
    {
        $initializers = [
            'value' => function (ContentFieldAccessor $instance) use ($field, $fieldDefinition) {
                return $this->getValue($field, $fieldDefinition);
            },
        ];

        return ContentFieldAccessor::createLazyGhost($initializers);
    }

    protected function getValue(Field $field, FieldDefinition $fieldDefinition)
    {
        $transformer = $this->contentFieldValueTransformers[$field->fieldTypeIdentifier] ?? null;
        if ($transformer) {
            return $transformer($field, $fieldDefinition);
        }

        return $field->value;
    }
}
