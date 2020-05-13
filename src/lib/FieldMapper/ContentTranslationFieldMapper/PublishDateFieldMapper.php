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

use eZ\Publish\Core\Search\Common\FieldRegistry;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandler;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentTranslationFieldMapper;

class PublishDateFieldMapper extends ContentTranslationFieldMapper
{
    /**
     * Field name, untyped.
     *
     * @var string
     */
    private static $fieldName = 'meta_publishdate__date';

    /**
     * @var array
     */
    protected $fieldIdentifiers = [];

    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    protected $contentTypeHandler;

    /**
     * @var \eZ\Publish\Core\Search\Common\FieldRegistry
     */
    protected $fieldRegistry;

    /**
     * PublishDateFieldMapper constructor.
     */
    public function __construct(ContentTypeHandler $contentTypeHandler, FieldRegistry $fieldRegistry)
    {
        $this->contentTypeHandler = $contentTypeHandler;
        $this->fieldRegistry      = $fieldRegistry;
    }

    public function setFieldIdentifiers(array $fieldIdentifiers): void
    {
        $this->fieldIdentifiers = $fieldIdentifiers;
    }

    /**
     * @param string $languageCode
     *
     * @return bool
     */
    public function accept(Content $content, $languageCode)
    {
        return true;
    }

    /**
     * @param string $languageCode
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return array|Field[]
     */
    public function mapFields(Content $content, $languageCode)
    {
        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId
        );

        foreach ($content->fields as $field) {
            if ($field->languageCode !== $languageCode) {
                continue;
            }

            foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                if (
                    $fieldDefinition->id !== $field->fieldDefinitionId
                    || (
                        !\in_array(
                            $fieldDefinition->identifier,
                            $this->fieldIdentifiers
                        )
                        && !\in_array(
                            "{$contentType->identifier}/{$fieldDefinition->identifier}",
                            $this->fieldIdentifiers
                        )
                    )
                ) {
                    continue;
                }

                $fieldType   = $this->fieldRegistry->getType($field->type);
                $indexFields = $fieldType->getIndexData($field, $fieldDefinition);

                foreach ($indexFields as $indexField) {
                    if (null === $indexField->value || !$indexField->type instanceof FieldType\DateField) {
                        continue;
                    }

                    return [
                        new Field(
                            static::$fieldName,
                            $indexField->value,
                            $indexField->type
                        ),
                    ];
                }
            }
        }

        return [
            new Field(
                static::$fieldName,
                $content->versionInfo->contentInfo->publicationDate,
                new FieldType\DateField()
            ),
        ];
    }
}
