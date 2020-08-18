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

namespace Novactive\EzEnhancedImageAsset\Imagine\Filter\Loader;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Novactive\EzEnhancedImageAsset\FocusPoint\FocusPointCalculator;

class FocusedThumbnailFilterLoader implements LoaderInterface
{
    /** @var FocusPointCalculator */
    protected $focusPointCalculator;

    /**
     * @required
     */
    public function setFocusPointCalculator(FocusPointCalculator $focusPointCalculator): void
    {
        $this->focusPointCalculator = $focusPointCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ImageInterface $image, array $options = []): ImageInterface
    {
        $imageSize = $image->getSize();
        $cropSize = $this->focusPointCalculator->calculateCropSize($imageSize, $options);
        if (null === $cropSize) {
            return $image;
        }

        $focusPoint = $options['focusPoint'] ?? new FocusPoint($options['focus'][0] ?? 0, $options['focus'][1] ?? 0);
        $originalSize = $options['originalSize'] ?? $imageSize;

        if (
            $originalSize->getWidth() !== $imageSize->getWidth() ||
            $originalSize->getHeight() !== $imageSize->getHeight()
        ) {
            $focusPoint = $this->focusPointCalculator->calculateCropFocusPoint(
                $originalSize,
                $imageSize,
                $focusPoint
            );
        }

        $ratios = [
            $cropSize->getWidth() / $imageSize->getWidth(),
            $cropSize->getHeight() / $imageSize->getHeight(),
        ];

        $thumbnail = $image->copy();

        $thumbnail->usePalette($image->palette());
        $thumbnail->strip();

        $ratio = max($ratios);
        $imageSize = $thumbnail->getSize()->scale($ratio);
        $thumbnail->resize($imageSize);

        $cropStartPoint = $this->focusPointCalculator->calculateCropStartPoint($imageSize, $cropSize, $focusPoint);

        $thumbnail->crop(
            $cropStartPoint,
            $cropSize
        );

        return $thumbnail;
    }
}
