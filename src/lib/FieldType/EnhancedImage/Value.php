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
     * @var FocusPoint
     */
    public $focusPoint;

    /**
     * After editing content, tell if focus point changed or not.
     *
     * @var bool
     */
    public $isNewFocusPoint = false;

    /**
     * Value constructor.
     *
     * @throws InvalidArgumentType
     */
    public function __construct(array $imageData = [])
    {
        $this->focusPoint = new FocusPoint();
        foreach ($imageData as $key => $value) {
            try {
                $this->$key = $value;
            } catch (PropertyNotFoundException $e) {
                throw new InvalidArgumentType(sprintf('EnhancedImage\Value::$%s', $key), 'Existing property', $value);
            }
        }
    }

    /**
     * Creates a value only from a file path.
     *
     * @param string $path
     *
     * @throws InvalidArgumentType
     *
     * @deprecated Starting with 5.3.3, handled by Image\Type::acceptValue()
     */
    public static function fromString($path): Value
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentType('$path', 'existing file', $path);
        }

        return new static(
            [
                'inputUri' => $path,
                'fileName' => basename($path),
                'fileSize' => filesize($path),
            ]
        );
    }
}
