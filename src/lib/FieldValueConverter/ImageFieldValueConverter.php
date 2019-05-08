<?php
/**
 * @copyright Novactive
 * Date: 06/05/19
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\FieldValueConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException;
use eZ\Publish\API\Repository\Exceptions\ContentValidationException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use eZ\Publish\SPI\Variation\VariationHandler;

class ImageFieldValueConverter implements FieldValueConverterInterface
{
    /** @var VariationHandler */
    protected $imageVariationService;

    /** @var ContentService */
    protected $contentService;

    /** @var LocationService */
    protected $locationService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var array */
    protected $mappings = [];

    /**
     * ImageFieldValueConverter constructor.
     *
     * @param array $mappings
     */
    public function __construct(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * @param VariationHandler $imageVariationService
     *
     * @required
     */
    public function setImageVariationService(VariationHandler $imageVariationService): void
    {
        $this->imageVariationService = $imageVariationService;
    }

    /**
     * @param ContentService $contentService
     *
     * @required
     */
    public function setContentService(ContentService $contentService): void
    {
        $this->contentService = $contentService;
    }

    /**
     * @param LocationService $locationService
     *
     * @required
     */
    public function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    /**
     * @param ContentTypeService $contentTypeService
     *
     * @required
     */
    public function setContentTypeService(ContentTypeService $contentTypeService): void
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @inheritDoc
     */
    public function support(string $fieldTypeIdentifier): bool
    {
        return 'ezimage' === $fieldTypeIdentifier;
    }

    /**
     * @param Content $content
     * @param Field   $field
     *
     * @throws NotFoundException
     * @throws BadStateException
     * @throws ContentFieldValidationException
     * @throws ContentValidationException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws InvalidArgumentType
     *
     * @return ImageAssetValue|null
     */
    public function toImageAssetValue(Content $content, Field $field): ?ImageAssetValue
    {
        /** @var ImageValue $fieldValue */
        $fieldValue = $field->value;
        if (null === $fieldValue || $fieldValue == new ImageValue()) {
            return null;
        }

        $this->imageVariationService->getVariation($field, $content->versionInfo, 'placeholder');
        $aasetRemoteId = "content-{$content->id}_{$field->fieldDefIdentifier}_asset";
        try {
            $imageContent = $this->getAsset($aasetRemoteId);
            $this->updateAsset(
                $imageContent,
                $fieldValue->fileName,
                $fieldValue,
                $field->languageCode
            );
        } catch (NotFoundException $e) {
            $imageContent = $this->createAsset(
                $fieldValue->fileName,
                $aasetRemoteId,
                $fieldValue,
                $field->languageCode
            );
        }

        return new ImageAssetValue(
            (int) $imageContent->id,
            $fieldValue->alternativeText
        );
    }

    /**
     * @param string $remoteId
     *
     * @throws UnauthorizedException
     * @throws NotFoundException
     *
     * @return Content
     */
    protected function getAsset(string $remoteId): Content
    {
        return $this->contentService->loadContentByRemoteId($remoteId);
    }

    /**
     * @param string     $name
     * @param string     $remoteId
     * @param ImageValue $image
     * @param string     $languageCode
     *
     * @throws NotFoundException
     * @throws BadStateException
     * @throws ContentFieldValidationException
     * @throws ContentValidationException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     *
     * @return Content
     */
    protected function createAsset(string $name, string $remoteId, ImageValue $image, string $languageCode): Content
    {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->mappings['content_type_identifier']
        );

        $contentCreateStruct           = $this->contentService->newContentCreateStruct($contentType, $languageCode);
        $contentCreateStruct->remoteId = $remoteId;
        $contentCreateStruct->setField($this->mappings['name_field_identifier'], $name, $languageCode);
        $contentCreateStruct->setField($this->mappings['content_field_identifier'], $image, $languageCode);

        $contentDraft = $this->contentService->createContent(
            $contentCreateStruct,
            [
                $this->locationService->newLocationCreateStruct($this->mappings['parent_location_id']),
            ]
        );

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }

    /**
     * @param Content    $content
     * @param string     $name
     * @param ImageValue $image
     * @param string     $languageCode
     *
     * @throws BadStateException
     * @throws ContentFieldValidationException
     * @throws ContentValidationException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     *
     * @return Content
     */
    protected function updateAsset(Content $content, string $name, ImageValue $image, string $languageCode): Content
    {
        $contentDraft = $this->contentService->createContentDraft($content->contentInfo);

        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        $contentUpdateStruct->setField($this->mappings['name_field_identifier'], $name, $languageCode);
        $contentUpdateStruct->setField($this->mappings['content_field_identifier'], $image, $languageCode);

        $contentDraft = $this->contentService->updateContent(
            $contentDraft->versionInfo,
            $contentUpdateStruct
        );

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }
}
