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

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\ImageAsset\AssetMapper;
use eZ\Publish\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use eZ\Publish\Core\FieldType\Relation\Value;

class ObjectRelationFieldValueConverter implements FieldValueConverterInterface
{
    /** @var ContentService */
    protected $contentService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var AssetMapper */
    protected $assetMapper;

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
    public function setContentTypeService(ContentTypeService $contentTypeService): void
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @required
     */
    public function setAssetMapper(AssetMapper $assetMapper): void
    {
        $this->assetMapper = $assetMapper;
    }

    /**
     * @inheritDoc
     */
    public function support(string $fieldTypeIdentifier): bool
    {
        return 'ezobjectrelation' === $fieldTypeIdentifier;
    }

    /**
     * @throws UnauthorizedException
     *
     * @return ImageAssetValue
     */
    public function toImageAssetValue(Content $content, Field $field): ?ImageAssetValue
    {
        /** @var Value $fieldValue */
        $fieldValue = $field->value;
        if (null !== $fieldValue->destinationContentId) {
            try {
                $this->contentService->loadContent($fieldValue->destinationContentId);

                return new ImageAssetValue(
                    (int) $fieldValue->destinationContentId
                );
            } catch (NotFoundException $e) {
                return null;
            }
        }

        return null;
    }
}
