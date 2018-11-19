<?php
/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type as ContentType;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentTranslationFieldMapper;

class CustomFulltextFieldMapper extends ContentTranslationFieldMapper
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
     *
     * @param ContentType\Handler                                                   $contentTypeHandler
     * @param \eZ\Publish\Core\Search\Common\FieldRegistry                          $fieldRegistry
     * @param \eZ\Publish\Core\Search\Common\FieldNameGenerator                     $fieldNameGenerator
     * @param \EzSystems\EzPlatformSolrSearchEngine\FieldMapper\BoostFactorProvider $boostFactorProvider
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

    /**
     * @param array $fieldsConfig
     */
    public function setFieldsConfig(array $fieldsConfig): void
    {
        $this->fieldsConfig = $fieldsConfig;
    }

    /**
     * @param Content $content
     * @param string  $languageCode
     *
     * @return bool
     */
    public function accept(Content $content, $languageCode)
    {
        return !empty($this->fieldsConfig);
    }

    /**
     * @param Content $content
     * @param string  $languageCode
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return array|Field[]
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

                    if (!$indexField->type instanceof FieldType\FullTextField || !$fieldDefinition->isSearchable) {
                        continue;
                    }

                    foreach ($fieldNames as $fieldName) {
                        $fields[] = new Field(
                            "meta_{$fieldName}__text",
                            $indexField->value,
                            $this->getIndexFieldType($contentType, $fieldName)
                        );
                    }
                }
            }
        }

        return $fields;
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
            if (\in_array(
                $fieldDefinition->identifier,
                $fieldIdentifiers
            ) || \in_array(
                "{$contentType->identifier}/{$fieldDefinition->identifier}",
                $fieldIdentifiers
            )) {
                $fieldNames[] = $fieldName;
            }
        }

        return $fieldNames;
    }

    /**
     * Return index field type for the given $contentType.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Type $contentType
     * @param string                                   $fieldName
     *
     * @return \eZ\Publish\SPI\Search\FieldType
     */
    private function getIndexFieldType(ContentType $contentType, $fieldName = 'text')
    {
        $newFieldType        = new FieldType\TextField();
        $newFieldType->boost = $this->boostFactorProvider->getContentMetaFieldBoostFactor(
            $contentType,
            $fieldName
        );

        return $newFieldType;
    }
}
