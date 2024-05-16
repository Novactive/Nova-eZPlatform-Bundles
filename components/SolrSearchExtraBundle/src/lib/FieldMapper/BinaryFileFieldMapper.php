<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\FieldMapper;

use Ibexa\Contracts\Core\Persistence\Content\Field as SPIField;
use Ibexa\Contracts\Core\Persistence\Content\Type as SPIContentType;
use Ibexa\Contracts\Core\Search\Field as SPISearchField;
use Ibexa\Contracts\Core\Search\FieldType as SPISearchFieldType;
use Ibexa\Core\IO\ConfigScopeChangeAwareIOService;
use Ibexa\Core\IO\Exception\BinaryFileNotFoundException;
use Ibexa\Core\IO\Values\BinaryFile;
use Ibexa\Solr\FieldMapper\BoostFactorProvider;
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
    
    /**
     * BinaryFileFieldMapper constructor.
     */
    public function __construct(
        protected ConfigScopeChangeAwareIOService $ioService,
        protected BoostFactorProvider $boostFactorProvider,
        protected TextExtractorInterface $textExtractor,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentValue
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
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
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentValue
     */
    private function getBinaryFileText(BinaryFile $binaryFile): ?string
    {
        $resource = $this->ioService->getFileInputStream($binaryFile);
        $resourceMetadata = stream_get_meta_data($resource);

        return $this->textExtractor->extract($resourceMetadata['uri']);
    }

    /**
     * Return index field type for the given $contentType.
     *
     * @return \Ibexa\Contracts\Core\Search\FieldType\TextField
     */
    private function getIndexFieldType(SPIContentType $contentType): SPISearchFieldType\TextField
    {
        $newFieldType = new SPISearchFieldType\TextField();
        $newFieldType->boost = $this->boostFactorProvider->getContentMetaFieldBoostFactor(
            $contentType,
            'text'
        );

        return $newFieldType;
    }
}
