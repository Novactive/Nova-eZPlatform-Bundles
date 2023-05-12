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

namespace Novactive\EzEnhancedImageAsset\Values;

use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;

/**
 * Class FocusedVariation.
 *
 * @package Novactive\EzEnhancedImageAsset\Values
 *
 * @property FocusPoint $focusPoint
 */
class FocusedVariation extends ImageVariation
{
    /** @var FocusPoint */
    protected $focusPoint;
}
