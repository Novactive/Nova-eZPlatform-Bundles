<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Solr\FieldMapper\ContentTranslationFieldMapper;
use Ibexa\Core\Search\Common\FieldRegistry;

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
     * PublishDateFieldMapper constructor.
     */
    public function __construct(
        protected ContentTypeHandler $contentTypeHandler,
        protected FieldRegistry $fieldRegistry,
        protected ConfigResolverInterface $configResolver
    ) {
    }

    public function setFieldIdentifiers(string $fieldIdentifiers): void
    {
        $this->fieldIdentifiers = $this->configResolver->getParameter($fieldIdentifiers, 'nova_solr_extra');
    }

    /**
     * @param string $languageCode
     */
    public function accept(Content $content, $languageCode): bool
    {
        return true;
    }

    /**
     * @param $languageCode
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function mapFields(Content $content, $languageCode): array
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
                        !in_array(
                            $fieldDefinition->identifier,
                            $this->fieldIdentifiers
                        )
                        && !in_array(
                            "{$contentType->identifier}/{$fieldDefinition->identifier}",
                            $this->fieldIdentifiers
                        )
                    )
                ) {
                    continue;
                }

                $fieldType = $this->fieldRegistry->getType($field->type);
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
