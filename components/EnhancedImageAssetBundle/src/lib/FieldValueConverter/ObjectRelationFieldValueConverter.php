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
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use Ibexa\Core\FieldType\Relation\Value;

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
     * {@inheritDoc}
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
