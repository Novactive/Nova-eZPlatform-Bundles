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

use Enzim\Lib\TikaWrapper\TikaWrapper;
use eZ\Publish\Core\IO\Exception\BinaryFileNotFoundException;
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
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Class BinaryFileFullTextFieldMapper.
 *
 * @package Novactive\EzSolrSearchExtra\FieldMapper\ContentTranslationFieldMapper
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

    /** @var LoggerInterface */
    private $logger;

    /**
     * List of field type which should be indexed.
     *
     * @var string[]
     */
    private $binaryFileFieldTypeIndentifiers = [];

    /**
     * BinaryFileFullTextFieldMapper constructor.
     *
     * @param IOService                     $ioService
     * @param ContentTypePersistenceHandler $contentTypeHandler
     * @param BoostFactorProvider           $boostFactorProvider
     * @param LoggerInterface               $logger
     * @param string[]                      $binaryFileFieldTypeIndentifiers
     */
    public function __construct(
        IOService $ioService,
        ContentTypePersistenceHandler $contentTypeHandler,
        BoostFactorProvider $boostFactorProvider,
        LoggerInterface $logger,
        array $binaryFileFieldTypeIndentifiers
    ) {
        $this->ioService                       = $ioService;
        $this->contentTypeHandler              = $contentTypeHandler;
        $this->boostFactorProvider             = $boostFactorProvider;
        $this->logger                          = $logger;
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
                $binaryFile = $this->ioService->loadBinaryFile($field->value->externalData['id']);
                $plaintext  = $this->getBinaryFileText($binaryFile);

                return new SPISearchField(
                    self::$fieldName,
                    $plaintext ?? '',
                    $this->getIndexFieldType($contentType)
                );
            } catch (BinaryFileNotFoundException $e) {
                $this->logger->warning($e->getMessage());
            }
        }

        return null;
    }

    /**
     * @param BinaryFile $binaryFile
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     *
     * @return null|string|string[]
     */
    private function getBinaryFileText(BinaryFile $binaryFile)
    {
        $resource         = $this->ioService->getFileInputStream($binaryFile);
        $resourceMetadata = stream_get_meta_data($resource);
        $fileUri = $resourceMetadata['uri'];
        try
        {
            $plaintext = TikaWrapper::getText( $fileUri );
        }catch (RuntimeException $e) {
            $errorMsg = $e->getMessage();
            $this->logger->error("Error when converting file $fileUri\n$errorMsg");
            return null;
        }

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
