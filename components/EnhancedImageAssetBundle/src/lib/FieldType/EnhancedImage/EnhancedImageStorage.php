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

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\FieldType\Image\ImageStorage;

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
                $binaryFile = $this->ioService->loadBinaryFile($field->value->externalData['id']);
                $stream = $this->ioService->getFileInputStream($binaryFile);
                $streamMetadatas = stream_get_meta_data($stream);

                $field->value->externalData['inputUri'] = $streamMetadatas['uri'];
            }
        }

        return parent::storeFieldData($versionInfo, $field, $context);
    }
}
