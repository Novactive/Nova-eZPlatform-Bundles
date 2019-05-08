<?php
/**
 * @copyright Novactive
 * Date: 06/05/19
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\FieldValueConverter;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\ImageAsset\Value as ImageAssetValue;

interface FieldValueConverterInterface
{
    /**
     * @param string $fieldTypeIdentifier
     *
     * @return bool
     */
    public function support(string $fieldTypeIdentifier): bool;

    /**
     * @param Content $content
     * @param Field   $field
     *
     * @return ImageAssetValue|null
     */
    public function toImageAssetValue(Content $content, Field $field): ?ImageAssetValue;
}
