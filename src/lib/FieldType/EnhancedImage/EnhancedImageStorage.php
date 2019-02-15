<?php
/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

namespace Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage;

use eZ\Publish\Core\FieldType\Image\ImageStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class EnhancedImageStorage extends ImageStorage
{
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $result = parent::storeFieldData($versionInfo, $field, $context);
        if (isset($field->value->data['path']) && $this->aliasCleaner) {
            $this->aliasCleaner->removeAliases($field->value->data['path']);
        }

        return $result;
    }
}
