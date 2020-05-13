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

use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypePersistenceHandler;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentTranslationFieldMapper;
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

    /**
     * List of field type which should be indexed.
     *
     * @var string[]
     */
    private $binaryFileFieldTypeIndentifiers = [];

    /**
     * Bool to enable indexation.
     *
     * @var bool
     */
    private $enabled = false;

    /**
     * BinaryFileFullTextFieldMapper constructor.
     *
     * @param string[] $binaryFileFieldTypeIndentifiers
     */
    public function __construct(
        BinaryFileFieldMapper $binaryFileFieldMapper,
        ContentTypePersistenceHandler $contentTypeHandler,
        array $binaryFileFieldTypeIndentifiers
    ) {
        $this->binaryFileFieldMapper           = $binaryFileFieldMapper;
        $this->contentTypeHandler              = $contentTypeHandler;
        $this->binaryFileFieldTypeIndentifiers = $binaryFileFieldTypeIndentifiers;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(SPIContent $content, $languageCode)
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function mapFields(SPIContent $content, $languageCode)
    {
        $fields = [];

        $contentType = $this->contentTypeHandler->load(
            $content->versionInfo->contentInfo->contentTypeId
        );

        foreach ($content->fields as $field) {
            if (
                $field->languageCode !== $languageCode
                 || !\in_array($field->type, $this->binaryFileFieldTypeIndentifiers)
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
