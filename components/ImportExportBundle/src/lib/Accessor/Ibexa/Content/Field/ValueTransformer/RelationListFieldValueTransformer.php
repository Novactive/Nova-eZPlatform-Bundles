<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessorBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;

class RelationListFieldValueTransformer implements FieldValueTransformerInterface
{
    public function __construct(
        protected ContentAccessorBuilder $contentAccessorBuilder
    ) {
    }

    /**
     * @return ContentAccessor[]
     */
    public function __invoke(Field $field, FieldDefinition $fieldDefinition): array
    {
        /** @var RelationListValue $fieldValue */
        $fieldValue = $field->getValue();
        if (empty($fieldValue->destinationContentIds)) {
            return [];
        }

        return array_map(function (int $contentId) {
            return $this->contentAccessorBuilder->buildFromContentId($contentId);
        }, $fieldValue->destinationContentIds);
    }
}
