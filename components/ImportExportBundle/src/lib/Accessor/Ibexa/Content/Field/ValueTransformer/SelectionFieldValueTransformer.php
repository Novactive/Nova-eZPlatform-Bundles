<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

class SelectionFieldValueTransformer implements FieldValueTransformerInterface
{
    /**
     * @return array<string, string>
     */
    public function __invoke(Field $field, FieldDefinition $fieldDefinition): array
    {
        $fieldValue = $field->getValue();

        return array_intersect_key(
            $fieldDefinition->fieldSettings['options'],
            array_flip($fieldValue->selection)
        );
    }
}
