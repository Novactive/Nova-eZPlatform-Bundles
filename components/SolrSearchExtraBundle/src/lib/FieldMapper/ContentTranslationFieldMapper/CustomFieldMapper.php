<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Type as ContentType;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Solr\FieldMapper\ContentTranslationFieldMapper;
use Ibexa\Core\Search\Common\FieldNameGenerator;
use Ibexa\Core\Search\Common\FieldRegistry;
use Ibexa\Solr\FieldMapper\BoostFactorProvider;

class CustomFieldMapper extends ContentTranslationFieldMapper
{
    /**
     * @var array
     */
    protected $fieldsConfig = [];
    
    /**
     * CustomFulltextFieldMapper constructor.
     */
    public function __construct(
        protected ContentType\Handler $contentTypeHandler,
        protected FieldRegistry $fieldRegistry,
        protected FieldNameGenerator $fieldNameGenerator,
        protected BoostFactorProvider $boostFactorProvider,
        protected ConfigResolverInterface $configResolver
    ) {
    }

    public function setFieldsConfig(string $customFields): void
    {
        $this->fieldsConfig = $this->configResolver->getParameter($customFields, 'nova_solr_extra');
    }

    /**
     * @param string $languageCode
     */
    public function accept(Content $content, $languageCode): bool
    {
        return !empty($this->fieldsConfig);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return array|\Ibexa\Contracts\Core\Search\Field[]
     */
    public function mapFields(Content $content, $languageCode): array
    {
        $fields = [];
        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId
        );

        foreach ($content->fields as $field) {
            if ($field->languageCode !== $languageCode) {
                continue;
            }

            foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                if ($fieldDefinition->id !== $field->fieldDefinitionId) {
                    continue;
                }

                $fieldNames = $this->getFieldNames($fieldDefinition, $contentType);
                if (empty($fieldNames)) {
                    continue;
                }

                $fieldType = $this->fieldRegistry->getType($field->type);
                $indexFields = $fieldType->getIndexData($field, $fieldDefinition);

                foreach ($indexFields as $indexField) {
                    if (null === $indexField->value) {
                        continue;
                    }

                    $this->appendField(
                        $fields,
                        $indexField,
                        $contentType,
                        $fieldDefinition,
                        $fieldNames
                    );
                }
            }
        }

        return $fields;
    }

    protected function appendField(
        array &$fields,
        Field $indexField,
        ContentType $contentType,
        FieldDefinition $fieldDefinition,
        array $fieldNames
    ): void {
        if ($indexField->type instanceof FieldType\FullTextField) {
            return;
        }

        foreach ($fieldNames as $fieldName) {
            $fields[] = new Field(
                $this->fieldNameGenerator->getName(
                    $indexField->name,
                    $fieldName
                ),
                $indexField->value,
                $this->getIndexFieldType($contentType, $fieldDefinition, $indexField->type)
            );
        }
    }

    /**
     * @param $fieldDefinition
     * @param $contentType
     */
    protected function getFieldNames($fieldDefinition, $contentType): array
    {
        $fieldNames = [];
        foreach ($this->fieldsConfig as $fieldName => $fieldIdentifiers) {
            if (
                in_array(
                    $fieldDefinition->identifier,
                    $fieldIdentifiers
                ) || in_array(
                    "{$contentType->identifier}/{$fieldDefinition->identifier}",
                    $fieldIdentifiers
                )
            ) {
                $fieldNames[] = $fieldName;
            }
        }

        return $fieldNames;
    }

    /**
     * Return index field type for the given arguments.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDefinition
     */
    private function getIndexFieldType(
        ContentType $contentType,
        ContentType\FieldDefinition $fieldDefinition,
        FieldType $fieldType
    ): FieldType {
        if (!$fieldType instanceof FieldType\TextField) {
            return $fieldType;
        }

        $fieldType = clone $fieldType;
        $fieldType->boost = $this->boostFactorProvider->getContentFieldBoostFactor(
            $contentType,
            $fieldDefinition
        );

        return $fieldType;
    }
}
