<?php
/**
 * ezplatform-demo.
 *
 * @package   ezplatform-demo
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 */

namespace Novactive\EzSolrSearchExtras\FieldMapper\ContentTranslationFieldMapper;

use Enzim\Lib\TikaWrapper\TikaWrapper;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\IO\IOService;
use eZ\Publish\Core\IO\Values\BinaryFile;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Field as SPIField;
use eZ\Publish\SPI\Persistence\Content\Type as SPIContentType;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypePersistenceHandler;
use eZ\Publish\SPI\Search\Field as SPISearchField;
use eZ\Publish\SPI\Search\FieldType as SPISearchFieldType;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\BoostFactorProvider;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentTranslationFieldMapper;

/**
 * Class BinaryFileFullTextFieldMapper.
 *
 * @package Novactive\EzSolrSearchExtras\FieldMapper\ContentTranslationFieldMapper
 */
class BinaryFileFullTextFieldMapper extends ContentTranslationFieldMapper
{
    /**
     * Field name, untyped.
     *
     * @var string
     */
    private static $fieldName = 'meta_content__text';

    /** @var IOService */
    private $ioService;

    /** @var ContentTypePersistenceHandler */
    private $contentTypeHandler;

    /** @var BoostFactorProvider */
    private $boostFactorProvider;

    /** @var string[] */
    private $binaryFileFieldTypeIndentifiers = [];

    /**
     * BinaryFileFullTextFieldMapper constructor.
     *
     * @param IOService                     $ioService
     * @param ContentTypePersistenceHandler $contentTypeHandler
     * @param BoostFactorProvider           $boostFactorProvider
     * @param string[]                      $binaryFileFieldTypeIndentifiers
     */
    public function __construct(
        IOService $ioService,
        ContentTypePersistenceHandler $contentTypeHandler,
        BoostFactorProvider $boostFactorProvider,
        array $binaryFileFieldTypeIndentifiers
    ) {
        $this->ioService                       = $ioService;
        $this->contentTypeHandler              = $contentTypeHandler;
        $this->boostFactorProvider             = $boostFactorProvider;
        $this->binaryFileFieldTypeIndentifiers = $binaryFileFieldTypeIndentifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(SPIContent $content, $languageCode)
    {
        return true;
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
            if ($field->languageCode !== $languageCode
                 || !\in_array($field->type, $this->binaryFileFieldTypeIndentifiers)) {
                continue;
            }

            $indexField = $this->mapField($field, $contentType);

            if (!$indexField) {
                continue;
            }
            $fields[] = $indexField;
        }

        return $fields;
    }

    /**
     * @param SPIField       $field
     * @param SPIContentType $contentType
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     *
     * @return SPISearchField|null
     */
    private function mapField(SPIField $field, SPIContentType $contentType): ?SPISearchField
    {
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition->id !== $field->fieldDefinitionId
                 || !$fieldDefinition->isSearchable
                 || !$field->value->externalData) {
                continue;
            }

            try {
                $plaintext = $this->getBinaryFileText(
                    $this->ioService->loadBinaryFile($field->value->externalData['id'])
                );
            } catch (NotFoundException $e) {
                return null;
            }

            return new SPISearchField(
                self::$fieldName,
                $plaintext ?? '',
                $this->getIndexFieldType($contentType)
            );
        }

        return null;
    }

    /**
     * Return the plaintext value of a binary file.
     *
     * @param BinaryFile $binaryFile
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     *
     * @return string
     */
    private function getBinaryFileText(BinaryFile $binaryFile)
    {
        $resource         = $this->ioService->getFileInputStream($binaryFile);
        $resourceMetadata = stream_get_meta_data($resource);
        $plaintext        = TikaWrapper::getText($resourceMetadata['uri']);

        // replace "tab" (hex 9) chars by space
        $cleanText = preg_replace('([\x09]+)', ' ', (string) $plaintext);

        return $cleanText;
    }

    /**
     * Return index field type for the given $contentType.
     *
     * @param SPIContentType $contentType
     *
     * @return SPISearchFieldType\TextField
     */
    private function getIndexFieldType(SPIContentType $contentType)
    {
        $newFieldType        = new SPISearchFieldType\TextField();
        $newFieldType->boost = $this->boostFactorProvider->getContentMetaFieldBoostFactor(
            $contentType,
            'text'
        );

        return $newFieldType;
    }
}
