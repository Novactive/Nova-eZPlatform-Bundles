<?php

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\FieldValueConverter;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;

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

    /** @var ConfigResolverInterface */
    protected $configResolver;

    /**
     * ImageFieldValueConverter constructor.
     */
    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @required
     */
    public function setImageVariationService(VariationHandler $imageVariationService): void
    {
        $this->imageVariationService = $imageVariationService;
    }

    /**
     * @required
     */
    public function setContentService(ContentService $contentService): void
    {
        $this->contentService = $contentService;
    }

    /**
     * @required
     */
    public function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    /**
     * @required
     */
    public function setContentTypeService(ContentTypeService $contentTypeService): void
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * {@inheritDoc}
     */
    public function support(string $fieldTypeIdentifier): bool
    {
        return 'ezimage' === $fieldTypeIdentifier;
    }

    /**
     * @throws BadStateException
     * @throws ContentFieldValidationException
     * @throws ContentValidationException
     * @throws InvalidArgumentException
     * @throws InvalidArgumentType
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    public function toImageAssetValue(Content $content, Field $field): ?ImageAssetValue
    {
        /** @var ImageValue $fieldValue */
        $fieldValue = $field->value;
        if (null === $fieldValue || $fieldValue === new ImageValue()) {
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
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    protected function getAsset(string $remoteId): Content
    {
        return $this->contentService->loadContentByRemoteId($remoteId);
    }

    /**
     * @throws BadStateException
     * @throws ContentFieldValidationException
     * @throws ContentValidationException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    protected function createAsset(string $name, string $remoteId, ImageValue $image, string $languageCode): Content
    {
        $mappings = $this->getMappings();
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $mappings['content_type_identifier']
        );

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $languageCode);
        $contentCreateStruct->remoteId = $remoteId;
        $contentCreateStruct->setField($mappings['name_field_identifier'], $name, $languageCode);
        $contentCreateStruct->setField($mappings['content_field_identifier'], $image, $languageCode);

        $contentDraft = $this->contentService->createContent(
            $contentCreateStruct,
            [
                $this->locationService->newLocationCreateStruct($mappings['parent_location_id']),
            ]
        );

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }

    /**
     * @throws BadStateException
     * @throws ContentFieldValidationException
     * @throws ContentValidationException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    protected function updateAsset(Content $content, string $name, ImageValue $image, string $languageCode): Content
    {
        $mappings = $this->getMappings();
        $contentDraft = $this->contentService->createContentDraft($content->contentInfo);

        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        $contentUpdateStruct->setField($mappings['name_field_identifier'], $name, $languageCode);
        $contentUpdateStruct->setField($mappings['content_field_identifier'], $image, $languageCode);

        $contentDraft = $this->contentService->updateContent(
            $contentDraft->versionInfo,
            $contentUpdateStruct
        );

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }

    protected function getMappings(): array
    {
        return $this->configResolver->getParameter('fieldtypes.ezimageasset.mappings');
    }
}
