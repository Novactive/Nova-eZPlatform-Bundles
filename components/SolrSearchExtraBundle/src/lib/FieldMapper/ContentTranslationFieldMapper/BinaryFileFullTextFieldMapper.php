<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypePersistenceHandler;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Solr\FieldMapper\ContentTranslationFieldMapper;
use Novactive\EzSolrSearchExtra\FieldMapper\BinaryFileFieldMapper;

/**
 * Class BinaryFileFullTextFieldMapper.
 *
 * @package Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper
 */
class BinaryFileFullTextFieldMapper extends ContentTranslationFieldMapper
{
    /**
     * Bool to enable indexation.
     */
    private bool $enabled = false;

    /**
     * BinaryFileFullTextFieldMapper constructor.
     *
     * @param string[] $binaryFileFieldTypeIdentifiers
     */
    public function __construct(
        private BinaryFileFieldMapper $binaryFileFieldMapper,
        private ContentTypePersistenceHandler $contentTypeHandler,
        private ConfigResolverInterface $configResolver,
        private array $binaryFileFieldTypeIdentifiers = []
    ) {
    }

    public function setEnabled(string $enabled): void
    {
        $this->enabled = $this->configResolver->getParameter($enabled, 'nova_solr_extra');
    }

    /**
     * {@inheritdoc}
     */
    public function accept(SPIContent $content, string $languageCode): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function mapFields(SPIContent $content, string $languageCode): array
    {
        $fields = [];

        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId
        );

        foreach ($content->fields as $field) {
            if (
                $field->languageCode !== $languageCode ||
                 !in_array($field->type, $this->binaryFileFieldTypeIdentifiers)
            ) {
                continue;
            }

            $indexField = $this->binaryFileFieldMapper->mapField($field, $contentType);

            if (!$indexField) {
                continue;
            }
            $fields[] = $indexField;
        }

        return $fields;
    }
}
