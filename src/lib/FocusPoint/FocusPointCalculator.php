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

namespace Novactive\EzEnhancedImageAsset\FocusPoint;

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Point;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;

class FocusPointCalculator
{
    /**
     * @param BoxInterface $imageSize
     * @param BoxInterface $cropSize
     * @param FocusPoint   $focusPoint
     *
     * @return FocusPoint
     */
    public function calculateCropFocusPoint(
        BoxInterface $imageSize,
        BoxInterface $cropSize,
        FocusPoint $focusPoint
    ): FocusPoint {
        $cropStartPoint  = $this->calculateCropStartPoint($imageSize, $cropSize, $focusPoint);
        $imageFocusPoint = $this->toPixel($focusPoint, $imageSize);

        return $this->toCoordinate(
            new FocusPoint(
                $imageFocusPoint->getPosX() - $cropStartPoint->getX(),
                $imageFocusPoint->getPosY() - $cropStartPoint->getY()
            ),
            $cropSize
        );
    }

    /**
     * @param BoxInterface $imageSize
     * @param BoxInterface $cropSize
     * @param FocusPoint   $focusPoint
     *
     * @return Point
     */
    public function calculateCropStartPoint(
        BoxInterface $imageSize,
        BoxInterface $cropSize,
        FocusPoint $focusPoint
    ): Point {
        $imageFocusPoint = $this->toPixel($focusPoint, $imageSize);
        $posX            = $imageFocusPoint->getPosX() - ($cropSize->getWidth() / 2);
        $posY            = $imageFocusPoint->getPosY() - ($cropSize->getHeight() / 2);
        if ($posX < 0) {
            $posX = 0;
        }
        if ($posX + $cropSize->getWidth() > $imageSize->getWidth()) {
            $posX = $imageSize->getWidth() - $cropSize->getWidth();
        }
        if ($posY < 0) {
            $posY = 0;
        }
        if ($posY + $cropSize->getHeight() > $imageSize->getHeight()) {
            $posY = $imageSize->getHeight() - $cropSize->getHeight();
        }

        return new Point($posX, $posY);
    }

    /**
     * @param FocusPoint   $focusPoint
     * @param BoxInterface $imageSize
     *
     * @return FocusPoint
     */
    protected function toCoordinate(FocusPoint $focusPoint, BoxInterface $imageSize): FocusPoint
    {
        return new FocusPoint(
            round(($focusPoint->getPosX() / $imageSize->getWidth() - .5) * 2, 2),
            round(($focusPoint->getPosY() / $imageSize->getHeight() - .5) * -2, 2)
        );
    }

    /**
     * @param FocusPoint   $focusPoint
     * @param BoxInterface $imageSize
     *
     * @return FocusPoint
     */
    protected function toPixel(FocusPoint $focusPoint, BoxInterface $imageSize): FocusPoint
    {
        $percentFocusPoint = $this->toPercent($focusPoint);

        return new FocusPoint(
            $percentFocusPoint->getPosX() * $imageSize->getWidth() / 100,
            $percentFocusPoint->getPosY() * $imageSize->getHeight() / 100
        );
    }

    /**
     * @param FocusPoint $focusPoint
     *
     * @return FocusPoint
     */
    protected function toPercent(FocusPoint $focusPoint): FocusPoint
    {
        return new FocusPoint(
            (($focusPoint->getPosX() + 1) / 2) * 100,
            ((-$focusPoint->getPosY() + 1) / 2) * 100
        );
    }

    /**
     * @param BoxInterface $imageSize
     * @param array        $options
     *
     * @return Box|null
     */
    public function calculateCropSize(BoxInterface $imageSize, array $options): ?Box
    {
        $width  = $options['size'][0] ?? null;
        $height = $options['size'][1] ?? null;

        $origWidth  = $imageSize->getWidth();
        $origHeight = $imageSize->getHeight();

        if (null === $width || null === $height) {
            if (null === $height) {
                $height = (int) (($width / $origWidth) * $origHeight);
            } elseif (null === $width) {
                $width = (int) (($height / $origHeight) * $origWidth);
            }
        }

        $cropSize = new Box($width, $height);
        if ($cropSize->contains($imageSize)) {
            return null;
        }

        if (!$imageSize->contains($cropSize)) {
            $cropSize = new Box(
                min($imageSize->getWidth(), $cropSize->getWidth()),
                min($imageSize->getHeight(), $cropSize->getHeight())
            );
        }

        return $cropSize;
    }
}
