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
    /** @var BinaryFileFieldMapper */
    private $binaryFileFieldMapper;

    /** @var ContentTypePersistenceHandler */
    private $contentTypeHandler;

    /** @var ConfigResolverInterface */
    private $configResolver;

    /**
     * List of field type which should be indexed.
     *
     * @var string[]
     */
    private $binaryFileFieldTypeIdentifiers = [];

    /**
     * Bool to enable indexation.
     *
     * @var bool
     */
    private $enabled = false;

    /**
     * BinaryFileFullTextFieldMapper constructor.
     */
    public function __construct(
        BinaryFileFieldMapper $binaryFileFieldMapper,
        ContentTypePersistenceHandler $contentTypeHandler,
        ConfigResolverInterface $configResolver,
        array $binaryFileFieldTypeIdentifiers
    ) {
        $this->binaryFileFieldMapper = $binaryFileFieldMapper;
        $this->contentTypeHandler = $contentTypeHandler;
        $this->binaryFileFieldTypeIdentifiers = $binaryFileFieldTypeIdentifiers;
        $this->configResolver = $configResolver;
    }

    public function setEnabled(string $enabled): void
    {
        $this->enabled = $this->configResolver->getParameter($enabled, 'nova_solr_extra');
    }

    /**
     * {@inheritdoc}
     */
    public function accept(SPIContent $content, $languageCode): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function mapFields(SPIContent $content, $languageCode): array
    {
        $fields = [];

        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId
        );

        foreach ($content->fields as $field) {
            if (
                $field->languageCode !== $languageCode
                 || !in_array($field->type, $this->binaryFileFieldTypeIdentifiers)
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
