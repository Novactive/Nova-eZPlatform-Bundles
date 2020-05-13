<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type as ContentType;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentTranslationFieldMapper;

class CustomFieldMapper extends ContentTranslationFieldMapper
{
    /**
     * @var array
     */
    protected $fieldsConfig = [];

    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    protected $contentTypeHandler;

    /**
     * @var \eZ\Publish\Core\Search\Common\FieldRegistry
     */
    protected $fieldRegistry;

    /**
     * @var \eZ\Publish\Core\Search\Common\FieldNameGenerator
     */
    protected $fieldNameGenerator;

    /**
     * @var \EzSystems\EzPlatformSolrSearchEngine\FieldMapper\BoostFactorProvider
     */
    protected $boostFactorProvider;

    /**
     * CustomFulltextFieldMapper constructor.
     */
    public function __construct(
        ContentType\Handler $contentTypeHandler,
        \eZ\Publish\Core\Search\Common\FieldRegistry $fieldRegistry,
        \eZ\Publish\Core\Search\Common\FieldNameGenerator $fieldNameGenerator,
        \EzSystems\EzPlatformSolrSearchEngine\FieldMapper\BoostFactorProvider $boostFactorProvider
    ) {
        $this->contentTypeHandler  = $contentTypeHandler;
        $this->fieldRegistry       = $fieldRegistry;
        $this->fieldNameGenerator  = $fieldNameGenerator;
        $this->boostFactorProvider = $boostFactorProvider;
    }

    public function setFieldsConfig(array $fieldsConfig): void
    {
        $this->fieldsConfig = $fieldsConfig;
    }

    /**
     * @param string $languageCode
     *
     * @return bool
     */
    public function accept(Content $content, $languageCode)
    {
        return !empty($this->fieldsConfig);
    }

    /**
     * @param string $languageCode
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return array|\eZ\Publish\SPI\Search\Field[]
     */
    public function mapFields(Content $content, $languageCode)
    {
        $fields      = [];
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

                $fieldType   = $this->fieldRegistry->getType($field->type);
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
     *
     * @return array
     */
    protected function getFieldNames($fieldDefinition, $contentType)
    {
        $fieldNames = [];
        foreach ($this->fieldsConfig as $fieldName => $fieldIdentifiers) {
            if (
                \in_array(
                    $fieldDefinition->identifier,
                    $fieldIdentifiers
                ) || \in_array(
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
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     *
     * @return \eZ\Publish\SPI\Search\FieldType
     */
    private function getIndexFieldType(
        ContentType $contentType,
        ContentType\FieldDefinition $fieldDefinition,
        FieldType $fieldType
    ) {
        if (!$fieldType instanceof FieldType\TextField) {
            return $fieldType;
        }

        $fieldType        = clone $fieldType;
        $fieldType->boost = $this->boostFactorProvider->getContentFieldBoostFactor(
            $contentType,
            $fieldDefinition
        );

        return $fieldType;
    }
}
