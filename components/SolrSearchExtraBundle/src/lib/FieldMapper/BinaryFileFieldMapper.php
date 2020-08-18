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

namespace Novactive\EzSolrSearchExtra\FieldMapper;

use eZ\Publish\Core\IO\Exception\BinaryFileNotFoundException;
use eZ\Publish\Core\IO\IOService;
use eZ\Publish\Core\IO\Values\BinaryFile;
use eZ\Publish\SPI\Persistence\Content\Field as SPIField;
use eZ\Publish\SPI\Persistence\Content\Type as SPIContentType;
use eZ\Publish\SPI\Search\Field as SPISearchField;
use eZ\Publish\SPI\Search\FieldType as SPISearchFieldType;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\BoostFactorProvider;
use Novactive\EzSolrSearchExtra\TextExtractor\TextExtractorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BinaryFileFieldMapper.
 *
 * @package src\lib\FieldMapper
 */
class BinaryFileFieldMapper
{
    /**
     * Field name, untyped.
     *
     * @var string
     */
    private static $fieldName = 'meta_content__text';

    /** @var IOService */
    private $ioService;

    /** @var BoostFactorProvider */
    private $boostFactorProvider;

    /** @var TextExtractorInterface */
    private $textExtractor;

    /** @var LoggerInterface */
    private $logger;

    /**
     * BinaryFileFieldMapper constructor.
     */
    public function __construct(
        IOService $ioService,
        BoostFactorProvider $boostFactorProvider,
        TextExtractorInterface $textExtractor,
        LoggerInterface $logger
    ) {
        $this->ioService           = $ioService;
        $this->boostFactorProvider = $boostFactorProvider;
        $this->textExtractor       = $textExtractor;
        $this->logger              = $logger;
    }

    /**
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     */
    public function mapField(SPIField $field, SPIContentType $contentType): ?SPISearchField
    {
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            if (
                $fieldDefinition->id !== $field->fieldDefinitionId
                 || !$fieldDefinition->isSearchable
                 || !$field->value->externalData
            ) {
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
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     *
     * @return string|null
     */
    private function getBinaryFileText(BinaryFile $binaryFile)
    {
        $resource         = $this->ioService->getFileInputStream($binaryFile);
        $resourceMetadata = stream_get_meta_data($resource);

        return $this->textExtractor->extract($resourceMetadata['uri']);
    }

    /**
     * Return index field type for the given $contentType.
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
