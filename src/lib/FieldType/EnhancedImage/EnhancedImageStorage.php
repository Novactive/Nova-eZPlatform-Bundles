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
        if (isset($field->value->externalData)) {
            $isNewFocusPoint = $field->value->externalData['isNewFocusPoint'] ?? false;
            if ($isNewFocusPoint && !isset($field->value->externalData['inputUri'])) {
                if (isset($field->value->externalData['id'])) {
                    $binaryFile      = $this->IOService->loadBinaryFile($field->value->externalData['id']);
                    $stream          = $this->IOService->getFileInputStream($binaryFile);
                    $streamMetadatas = stream_get_meta_data($stream);

                    $field->value->externalData['inputUri'] = $streamMetadatas['uri'];
                }
            }
        }

        return parent::storeFieldData($versionInfo, $field, $context);
    }
}
