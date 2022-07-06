<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content\Type as ContentType;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;

class CustomFulltextFieldMapper extends CustomFieldMapper
{
    protected function appendField(
        array &$fields,
        Field $indexField,
        ContentType $contentType,
        FieldDefinition $fieldDefinition,
        array $fieldNames
    ): void {
        if (!$indexField->type instanceof FieldType\FullTextField || !$fieldDefinition->isSearchable) {
            return;
        }

        foreach ($fieldNames as $fieldName) {
            $fields[] = new Field(
                "meta_{$fieldName}__text",
                $indexField->value,
                $this->getIndexFieldType($contentType, $fieldName)
            );
        }
    }

    /**
     * Return index field type for the given $contentType.
     *
     * @return \Ibexa\Contracts\Core\Search\FieldType\TextField
     */
    private function getIndexFieldType(ContentType $contentType, string $fieldName = 'text'): FieldType\TextField
    {
        $newFieldType = new FieldType\TextField();
        $newFieldType->boost = $this->boostFactorProvider->getContentMetaFieldBoostFactor(
            $contentType,
            $fieldName
        );

        return $newFieldType;
    }
}
