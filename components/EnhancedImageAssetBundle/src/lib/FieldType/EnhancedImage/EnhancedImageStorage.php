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

namespace Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\FieldType\Image\ImageStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class EnhancedImageStorage extends ImageStorage
{
    /**
     * @throws InvalidArgumentException
     * @throws InvalidArgumentValue
     * @throws NotFoundException
     *
     * @return bool|mixed
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        if (isset($field->value->externalData)) {
            $isNewFocusPoint = $field->value->externalData['isNewFocusPoint'] ?? false;
            if (
                $isNewFocusPoint
                && !isset($field->value->externalData['inputUri'])
                && isset($field->value->externalData['id'])
            ) {
                $binaryFile      = $this->IOService->loadBinaryFile($field->value->externalData['id']);
                $stream          = $this->IOService->getFileInputStream($binaryFile);
                $streamMetadatas = stream_get_meta_data($stream);

                $field->value->externalData['inputUri'] = $streamMetadatas['uri'];
            }
        }

        return parent::storeFieldData($versionInfo, $field, $context);
    }
}
