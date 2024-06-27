<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessorBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;

class RelationFieldValueTransformer implements FieldValueTransformerInterface
{
    protected ContentAccessorBuilder $contentAccessorBuilder;

    public function __construct(
        ContentAccessorBuilder $contentAccessorBuilder
    ) {
        $this->contentAccessorBuilder = $contentAccessorBuilder;
    }

    public function __invoke(Field $field, FieldDefinition $fieldDefinition): ?ContentAccessor
    {
        /** @var RelationValue $fieldValue */
        $fieldValue = $field->getValue();
        if (null === $fieldValue->destinationContentId) {
            return null;
        }

        return $this->contentAccessorBuilder->buildFromContentId($fieldValue->destinationContentId);
    }
}
