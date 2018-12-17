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
declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;

/**
 * Class Value.
 *
 * @package Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage
 */
class Value extends ImageValue
{
    /**
     * FocusPoint.
     *
     * @var int
     */
    public $focusPoint;

    /**
     * Value constructor.
     *
     * @param array $imageData
     *
     * @throws InvalidArgumentType
     */
    public function __construct(array $imageData = [])
    {
        foreach ($imageData as $key => $value) {
            try {
                $this->$key = $value;
            } catch (PropertyNotFoundException $e) {
                throw new InvalidArgumentType(
                    sprintf('EnhancedImage\Value::$%s', $key),
                    'Existing property',
                    $value
                );
            }
        }
    }
}
